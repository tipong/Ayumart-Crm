<?php

namespace App\Services;

use App\Models\FonnteContact;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class FonnteService
{
    protected $deviceToken;  // Token untuk mengirim pesan
    protected $apiKey;       // Token untuk operasi API lainnya
    protected $baseUrl;

    public function __construct()
    {
        // Device token digunakan untuk mengirim pesan WhatsApp
        $this->deviceToken = config('services.fonnte.device_token');
        // API key digunakan untuk operasi lainnya (jika diperlukan)
        $this->apiKey = config('services.fonnte.api_key');
        $this->baseUrl = config('services.fonnte.base_url');
    }

    /**
     * Check if Fonnte API is properly configured
     */
    public function isConfigured(): bool
    {
        $configured = !empty($this->deviceToken) && !empty($this->baseUrl);

        if (!$configured) {
            Log::warning('Fonnte: not configured', [
                'deviceToken' => !empty($this->deviceToken) ? '***set***' : 'NOT_SET',
                'baseUrl' => !empty($this->baseUrl) ? '***set***' : 'NOT_SET'
            ]);
        }

        return $configured;
    }

    /**
     * Get all contacts from Fonnte database
     */
    public function getContacts(int $limit = 100, int $offset = 0)
    {
        if (!$this->isConfigured()) {
            Log::warning('Fonnte: not configured');
            return [
                'success' => false,
                'error' => 'Fonnte not configured',
                'contacts' => [],
                'total_items' => 0
            ];
        }

        try {
            $query = FonnteContact::query();
            $totalItems = $query->count();
            $contacts = $query->orderBy('created_at', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            Log::info('Fonnte: Successfully fetched contacts from database', [
                'total' => $totalItems,
                'limit' => $limit,
                'offset' => $offset,
                'returned' => $contacts->count()
            ]);

            return [
                'success' => true,
                'contacts' => $contacts->toArray(),
                'total_items' => $totalItems,
                'error' => null,
                'source' => 'local_database'
            ];
        } catch (Exception $e) {
            Log::error('Fonnte: Exception fetching contacts from database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'contacts' => [],
                'total_items' => 0
            ];
        }
    }

    /**
     * Get total count of Fonnte contacts
     */
    public function getContactsCount()
    {
        if (!$this->isConfigured()) {
            return 0;
        }

        try {
            return FonnteContact::count();
        } catch (Exception $e) {
            Log::error('Fonnte: Exception getting contacts count', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Add a new contact
     */
    public function addContact($email, $name = '', $phone = '', $customFields = [])
    {
        if (!$this->isConfigured()) {
            $errorMsg = 'Fonnte API tidak terkonfigurasi. ';

            if (empty($this->deviceToken)) {
                $errorMsg .= 'FONNTE_DEVICE_TOKEN tidak diset. ';
            }
            if (empty($this->baseUrl)) {
                $errorMsg .= 'FONNTE_BASE_URL tidak diset. ';
            }

            Log::error('Fonnte: not configured', [
                'error' => $errorMsg,
                'email' => $email,
                'phone' => $phone
            ]);

            return [
                'success' => false,
                'error' => trim($errorMsg) . ' Silakan atur di file .env'
            ];
        }

        try {
            $existing = FonnteContact::where('phone', $phone)->orWhere('email', $email)->first();
            if ($existing) {
                return [
                    'success' => false,
                    'error' => 'Contact already exists with this phone or email'
                ];
            }

            $contact = FonnteContact::create([
                'nome' => $name,
                'phone' => $phone,
                'email' => $email,
                'variable' => !empty($customFields) ? $customFields : null,
            ]);

            Log::info('Fonnte: Successfully added contact to database', [
                'email' => $email,
                'phone' => $phone,
                'contact_id' => $contact->id
            ]);

            return [
                'success' => true,
                'contact_id' => $contact->id,
                'data' => $contact,
                'error' => null
            ];
        } catch (Exception $e) {
            Log::error('Fonnte: Exception adding contact', [
                'email' => $email,
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update a contact
     */
    public function updateContact($contactId, $email = null, $name = null, $phone = null, $customFields = [])
    {
        if (!$this->isConfigured()) {
            Log::warning('Fonnte: not configured');
            return [
                'success' => false,
                'error' => 'Fonnte not configured'
            ];
        }

        try {
            $contact = FonnteContact::find($contactId);
            if (!$contact) {
                return [
                    'success' => false,
                    'error' => 'Contact not found'
                ];
            }

            $updateData = [];
            if ($email !== null) {
                $updateData['email'] = $email;
            }
            if ($name !== null) {
                $updateData['nome'] = $name;
            }
            if ($phone !== null) {
                $updateData['phone'] = $phone;
            }
            if (!empty($customFields)) {
                $updateData['variable'] = $customFields;
            }

            $contact->update($updateData);

            Log::info('Fonnte: Successfully updated contact in database', [
                'contact_id' => $contactId
            ]);

            return [
                'success' => true,
                'data' => $contact,
                'error' => null
            ];
        } catch (Exception $e) {
            Log::error('Fonnte: Exception updating contact', [
                'contact_id' => $contactId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete a contact
     */
    public function deleteContact($contactId)
    {
        if (!$this->isConfigured()) {
            Log::warning('Fonnte: not configured');
            return [
                'success' => false,
                'error' => 'Fonnte not configured'
            ];
        }

        try {
            $contact = FonnteContact::find($contactId);
            if (!$contact) {
                return [
                    'success' => false,
                    'error' => 'Contact not found'
                ];
            }

            $contact->delete();

            Log::info('Fonnte: Successfully deleted contact from database', [
                'contact_id' => $contactId
            ]);

            return [
                'success' => true,
                'error' => null
            ];
        } catch (Exception $e) {
            Log::error('Fonnte: Exception deleting contact', [
                'contact_id' => $contactId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get a single contact by ID
     */
    public function getContact($contactId)
    {
        if (!$this->isConfigured()) {
            Log::warning('Fonnte: not configured');
            return [
                'success' => false,
                'error' => 'Fonnte not configured'
            ];
        }

        try {
            $contact = FonnteContact::find($contactId);
            if (!$contact) {
                return [
                    'success' => false,
                    'error' => 'Contact not found'
                ];
            }

            Log::info('Fonnte: Successfully fetched contact from database', [
                'contact_id' => $contactId
            ]);

            return [
                'success' => true,
                'data' => $contact,
                'error' => null
            ];
        } catch (Exception $e) {
            Log::error('Fonnte: Exception fetching contact', [
                'contact_id' => $contactId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Search contacts by email
     */
    public function searchByEmail($email)
    {
        if (!$this->isConfigured()) {
            Log::warning('Fonnte: not configured');
            return [
                'success' => false,
                'error' => 'Fonnte not configured',
                'contacts' => []
            ];
        }

        try {
            $contacts = FonnteContact::where('email', $email)->get();

            Log::info('Fonnte: Successfully searched contacts by email', [
                'email' => $email,
                'count' => $contacts->count()
            ]);

            return [
                'success' => true,
                'contacts' => $contacts,
                'error' => null
            ];
        } catch (Exception $e) {
            Log::error('Fonnte: Exception searching contacts', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'contacts' => []
            ];
        }
    }

    /**
     * Normalize phone number to Fonnte format
     * Converts: 0812345678 -> 62812345678
     *           812345678 -> 62812345678
     *           +62812345678 -> 62812345678
     *           62812345678 -> 62812345678
     */
    /**
     * Normalize phone number to Fonnte format (62XXXXXXXXXX)
     * Can be called from outside the class
     */
    public function normalizePhoneNumber($phone)
    {
        $phone = trim($phone);
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Remove + prefix if exists
        $phone = str_replace('+', '', $phone);

        // If starts with 0, replace with 62
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }

        // If doesn't start with 62, add it
        if (strpos($phone, '62') !== 0) {
            $phone = '62' . $phone;
        }

        // Ensure it starts with 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        return $phone;
    }

    /**
     * Send a message via Fonte WhatsApp API
     */
    public function sendMessage($phone, $message, $type = 'text', $attachmentUrl = null)
    {
        Log::debug('Fonnte: sendMessage called', ['phone' => $phone]);

        if (!$this->isConfigured()) {
            Log::warning('Fonnte: not configured');
            return [
                'success' => false,
                'error' => 'Fonnte not configured'
            ];
        }

        try {
            // Normalize phone number to format: 62XXXXXXXXXX
            $normalizedPhone = $this->normalizePhoneNumber($phone);

            Log::info('Fonnte: Sending message', [
                'original_phone' => $phone,
                'normalized_phone' => $normalizedPhone,
                'message_length' => strlen($message),
                'type' => $type,
                'endpoint' => $this->baseUrl . '/send'
            ]);

            // Build payload using form-data format (not JSON) as per Fonnte API docs
            $postFields = [
                'target' => $normalizedPhone,
                'message' => $message,
            ];

            if ($type === 'image' && $attachmentUrl) {
                $postFields['image'] = $attachmentUrl;
            } elseif ($type === 'document' && $attachmentUrl) {
                $postFields['document'] = $attachmentUrl;
            }

            // Use curl directly for single messages to ensure form-data is sent correctly
            // Laravel Http client defaults to JSON which Fonnte API rejects
            $curl = curl_init();

            Log::debug('Fonnte: Curl options', [
                'url' => $this->baseUrl . '/send',
                'device_token' => substr($this->deviceToken, 0, 5) . '...',
                'postfields' => $postFields
            ]);

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->baseUrl . '/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $this->deviceToken
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            if ($curlError) {
                Log::error('Fonnte: Curl error sending message', [
                    'phone' => $normalizedPhone,
                    'error' => $curlError
                ]);

                return [
                    'success' => false,
                    'error' => $curlError
                ];
            }

            $responseData = json_decode($response, true) ?? [];

            // Check if Fonnte API actually succeeded (not just HTTP 200)
            $apiSuccess = isset($responseData['status']) && $responseData['status'] === true;

            if ($apiSuccess) {
                Log::info('Fonnte: Successfully sent message', [
                    'phone' => $normalizedPhone,
                    'type' => $type,
                    'response_data' => $responseData
                ]);

                return [
                    'success' => true,
                    'data' => $responseData,
                    'error' => null
                ];
            } else {
                // API returned error
                $errorReason = $responseData['reason'] ?? 'Unknown error from Fonnte';
                Log::error('Fonnte: API returned error', [
                    'phone' => $normalizedPhone,
                    'type' => $type,
                    'response_data' => $responseData,
                    'error_reason' => $errorReason
                ]);

                return [
                    'success' => false,
                    'error' => $errorReason,
                    'data' => $responseData
                ];
            }
        } catch (Exception $e) {
            Log::error('Fonnte: Exception sending message', [
                'phone' => $phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send newsletter to all Fonte contacts via WhatsApp
     */
    /**
     * Send newsletter using Fonnte batch API (new method with 'data' parameter)
     * This method uses the new Fonnte API that allows sending multiple messages in one request
     * More efficient and will appear in Fonnte dashboard history
     */
    public function sendNewsletter($subject, $message, $deliveryMethod = 'text')
    {
        if (!$this->isConfigured()) {
            Log::warning('Fonnte: not configured for newsletter sending');
            return [
                'success' => false,
                'error' => 'Fonnte not configured',
                'total_sent' => 0,
                'total_failed' => 0,
                'messages' => []
            ];
        }

        try {
            $contactsResult = $this->getContacts(PHP_INT_MAX, 0);

            if (!$contactsResult['success']) {
                throw new Exception('Failed to fetch contacts: ' . $contactsResult['error']);
            }

            $contacts = $contactsResult['contacts'] ?? [];
            $totalContacts = count($contacts);

            if ($totalContacts === 0) {
                Log::warning('Fonnte: No contacts found for newsletter sending');
                return [
                    'success' => true,
                    'message' => 'No contacts to send newsletter to',
                    'total_sent' => 0,
                    'total_failed' => 0,
                    'messages' => []
                ];
            }

            // Prepare batch data for Fonnte API
            $batchData = [];
            $messages = [];
            $totalProcessed = 0;

            $formattedMessage = "📬 *{$subject}*\n\n{$message}";

            foreach ($contacts as $contact) {
                try {
                    if (empty($contact['phone'])) {
                        $messages[] = [
                            'phone' => $contact['phone'] ?? 'N/A',
                            'name' => $contact['nome'] ?? 'Unknown',
                            'success' => false,
                            'error' => 'No phone number'
                        ];
                        continue;
                    }

                    $normalizedPhone = $this->normalizePhoneNumber($contact['phone']);

                    // Add to batch data
                    $batchData[] = [
                        'target' => $normalizedPhone,
                        'message' => $formattedMessage,
                        'delay' => '1'  // 1 second delay between messages
                    ];

                    $totalProcessed++;
                    $messages[] = [
                        'phone' => $normalizedPhone,
                        'name' => $contact['nome'] ?? 'Unknown',
                        'queued' => true
                    ];
                } catch (Exception $e) {
                    $messages[] = [
                        'phone' => $contact['phone'] ?? 'Unknown',
                        'name' => $contact['nome'] ?? 'Unknown',
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            // If no valid contacts, return
            if (empty($batchData)) {
                Log::warning('Fonnte: No valid contacts to send', [
                    'total_contacts' => $totalContacts,
                    'valid_contacts' => 0
                ]);

                return [
                    'success' => false,
                    'error' => 'No valid contacts with phone numbers',
                    'total_sent' => 0,
                    'total_failed' => $totalContacts,
                    'total_contacts' => $totalContacts,
                    'messages' => $messages
                ];
            }

            // Send batch using new Fonnte API method with 'data' parameter
            $fonteResponse = $this->sendBatchMessages($batchData);

            if (!$fonteResponse['success']) {
                Log::error('Fonnte: Batch sending failed', [
                    'error' => $fonteResponse['error'],
                    'total_queued' => $totalProcessed
                ]);

                return [
                    'success' => false,
                    'error' => $fonteResponse['error'],
                    'total_sent' => 0,
                    'total_failed' => $totalContacts,
                    'total_contacts' => $totalContacts,
                    'messages' => $messages
                ];
            }

            // All messages queued successfully
            $totalSent = $totalProcessed;
            $totalFailed = $totalContacts - $totalProcessed;

            Log::info('Fonnte: Newsletter batch sent successfully', [
                'total_contacts' => $totalContacts,
                'total_queued' => $totalSent,
                'total_failed_validation' => $totalFailed,
                'subject' => $subject,
                'api_method' => 'batch_data'
            ]);

            return [
                'success' => true,
                'message' => "Newsletter queued to {$totalSent}/{$totalContacts} contacts",
                'total_sent' => $totalSent,
                'total_failed' => $totalFailed,
                'total_contacts' => $totalContacts,
                'messages' => $messages,
                'fontes_response' => $fonteResponse['data'] ?? null
            ];
        } catch (Exception $e) {
            Log::error('Fonnte: Exception sending newsletter batch', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'total_sent' => 0,
                'total_failed' => 0,
                'messages' => []
            ];
        }
    }

    /**
     * Send batch messages using Fonnte new API with 'data' parameter
     * This is the new recommended method from Fonnte documentation
     * Reference: Fonnte API docs - batch sending with 'data' parameter
     */
    /**
     * Send batch messages via Fonnte API
     * Can be called from outside the class
     */
    public function sendBatchMessages($batchData)
    {
        if (!$this->isConfigured()) {
            Log::warning('Fonnte: not configured for batch sending');
            return [
                'success' => false,
                'error' => 'Fonnte not configured'
            ];
        }

        try {
            Log::info('Fonnte: Sending batch messages', [
                'count' => count($batchData),
                'endpoint' => $this->baseUrl . '/send',
                'method' => 'batch_data'
            ]);

            // Convert batch data to JSON string as per Fonnte API requirement
            $dataJson = json_encode($batchData);

            // Use curl for batch sending (Fonnte batch API requires multipart form-data with 'data' parameter)
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->baseUrl . '/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'data' => $dataJson
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . $this->deviceToken
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            curl_close($curl);

            if ($curlError) {
                Log::error('Fonnte: Curl error in batch sending', [
                    'error' => $curlError,
                    'count' => count($batchData)
                ]);

                return [
                    'success' => false,
                    'error' => $curlError
                ];
            }

            $responseData = json_decode($response, true);

            Log::info('Fonnte: Batch API response received', [
                'http_code' => $httpCode,
                'response' => $responseData,
                'count' => count($batchData)
            ]);

            // Check if Fonnte API accepted the batch
            $apiSuccess = isset($responseData['status']) && $responseData['status'] === true;

            if ($apiSuccess) {
                Log::info('Fonnte: Batch messages accepted by API', [
                    'count' => count($batchData),
                    'response_data' => $responseData
                ]);

                return [
                    'success' => true,
                    'data' => $responseData,
                    'error' => null
                ];
            } else {
                $errorReason = $responseData['reason'] ?? 'Unknown error from Fontte';
                Log::error('Fonnte: Batch API returned error', [
                    'count' => count($batchData),
                    'response_data' => $responseData,
                    'error_reason' => $errorReason
                ]);

                return [
                    'success' => false,
                    'error' => $errorReason,
                    'data' => $responseData
                ];
            }
        } catch (Exception $e) {
            Log::error('Fonnte: Exception in batch sending', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'count' => count($batchData)
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get customers with phone numbers from tb_pelanggan table
     * This replaces the old FonnteContact method
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getCustomersWithPhones($limit = PHP_INT_MAX, $offset = 0)
    {
        try {
            // Get customers with non-null and non-empty phone numbers
            $query = Pelanggan::query()
                ->with('user')
                ->whereNotNull('no_tlp_pelanggan')
                ->where('no_tlp_pelanggan', '!=', '');

            $totalItems = $query->count();

            $customers = $query
                ->orderBy('id_pelanggan', 'desc')
                ->offset($offset)
                ->limit($limit)
                ->get();

            // Map to match expected format
            $contacts = $customers->map(function($pelanggan) {
                return [
                    'id' => $pelanggan->id_pelanggan,
                    'phone' => $pelanggan->no_tlp_pelanggan,
                    'nome' => $pelanggan->nama_pelanggan ?? 'Customer',
                    'name' => $pelanggan->nama_pelanggan ?? 'Customer',
                    'email' => $pelanggan->user->email ?? '',
                    'status' => $pelanggan->status_pelanggan ?? 'active'
                ];
            })->toArray();

            Log::info('Fonnte: Successfully fetched customers from tb_pelanggan', [
                'total' => $totalItems,
                'limit' => $limit,
                'offset' => $offset,
                'returned' => count($contacts)
            ]);

            return [
                'success' => true,
                'contacts' => $contacts,
                'total_items' => $totalItems,
                'error' => null,
                'source' => 'tb_pelanggan'
            ];
        } catch (Exception $e) {
            Log::error('Fonnte: Exception fetching customers from tb_pelanggan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'contacts' => [],
                'total_items' => 0
            ];
        }
    }
}
