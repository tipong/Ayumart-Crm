<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;
use Illuminate\Support\Facades\Log;
use Exception;

class MailchimpService
{
    protected $client;
    protected $listId;
    protected $apiKey;
    protected $serverPrefix;

    public function __construct()
    {
        $this->apiKey = config('mailchimp.api_key');
        $this->serverPrefix = config('mailchimp.server_prefix');
        $this->listId = config('mailchimp.list_id');

        $this->client = new ApiClient();

        $this->client->setConfig([
            'apiKey' => $this->apiKey,
            'server' => $this->serverPrefix,
        ]);
    }

    /**
     * Add or update a subscriber to the list
     */
    public function addOrUpdateSubscriber($email, $firstName = '', $lastName = '', $tags = [])
    {
        try {
            $subscriberHash = md5(strtolower($email));

            $response = $this->client->lists->setListMember($this->listId, $subscriberHash, [
                'email_address' => $email,
                'status_if_new' => 'subscribed',
                'merge_fields' => [
                    'FNAME' => $firstName,
                    'LNAME' => $lastName,
                ],
                'tags' => $tags,
            ]);

            return [
                'success' => true,
                'member_id' => $response->id ?? null,
                'data' => $response
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp add subscriber error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Remove a subscriber from the list
     */
    public function removeSubscriber($email)
    {
        try {
            $subscriberHash = md5(strtolower($email));

            $this->client->lists->deleteListMember($this->listId, $subscriberHash);

            return [
                'success' => true
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp remove subscriber error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get subscriber info
     */
    public function getSubscriber($email)
    {
        try {
            $subscriberHash = md5(strtolower($email));

            $response = $this->client->lists->getListMember($this->listId, $subscriberHash);

            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get total subscribers count
     */
    public function getListMembersCount()
    {
        try {
            $url = "https://{$this->serverPrefix}.api.mailchimp.com/3.0/lists/{$this->listId}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, "anystring:{$this->apiKey}");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new Exception("API returned HTTP code: {$httpCode}");
            }

            $data = json_decode($response, true);

            return [
                'success' => true,
                'count' => $data['stats']['member_count'] ?? 0
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp get list count error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'count' => 0
            ];
        }
    }

    /**
     * Get list members with pagination and filtering
     */
    public function getListMembers($count = 100, $offset = 0, $status = '')
    {
        try {
            $url = "https://{$this->serverPrefix}.api.mailchimp.com/3.0/lists/{$this->listId}/members";
            $url .= "?count={$count}&offset={$offset}";

            // Add status filter if provided (subscribed, unsubscribed, cleaned, pending)
            if ($status !== '') {
                $url .= "&status={$status}";
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, "anystring:{$this->apiKey}");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new Exception("API returned HTTP code: {$httpCode}");
            }

            $data = json_decode($response, true);

            return [
                'success' => true,
                'members' => $data['members'] ?? [],
                'total_items' => $data['total_items'] ?? 0,
                'data' => $data
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp get list members error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'members' => [],
                'total_items' => 0
            ];
        }
    }

    /**
     * Create a campaign
     */
    public function createCampaign($subject, $title, $fromName = null, $replyTo = null)
    {
        try {
            $response = $this->client->campaigns->create([
                'type' => 'regular',
                'recipients' => [
                    'list_id' => $this->listId,
                ],
                'settings' => [
                    'subject_line' => $subject,
                    'title' => $title,
                    'from_name' => $fromName ?? config('mailchimp.from_name'),
                    'reply_to' => $replyTo ?? config('mailchimp.reply_to'),
                ],
            ]);

            return [
                'success' => true,
                'campaign_id' => $response->id,
                'data' => $response
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp create campaign error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Set campaign content
     */
    public function setCampaignContent($campaignId, $htmlContent, $plainText = null)
    {
        try {
            $response = $this->client->campaigns->setContent($campaignId, [
                'html' => $htmlContent,
                'plain_text' => $plainText,
            ]);

            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp set campaign content error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send campaign
     */
    public function sendCampaign($campaignId)
    {
        try {
            $response = $this->client->campaigns->send($campaignId);

            return [
                'success' => true,
                'data' => $response
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp send campaign error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get campaign report/statistics
     */
    public function getCampaignReport($campaignId)
    {
        try {
            $response = $this->client->reports->getCampaignReport($campaignId);

            // Calculate rates
            $emailsSent = $response->emails_sent ?? 0;
            $uniqueOpens = $response->opens->unique_opens ?? 0;
            $uniqueClicks = $response->clicks->unique_clicks ?? 0;

            $openRate = $emailsSent > 0 ? ($uniqueOpens / $emailsSent) * 100 : 0;
            $clickRate = $emailsSent > 0 ? ($uniqueClicks / $emailsSent) * 100 : 0;

            return [
                'success' => true,
                'data' => $response,
                'opens' => $uniqueOpens,
                'clicks' => $uniqueClicks,
                'emails_sent' => $emailsSent,
                'open_rate' => $openRate,
                'click_rate' => $clickRate,
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp get campaign report error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get email activity for a campaign (who opened/clicked)
     */
    public function getCampaignEmailActivity($campaignId, $limit = 1000)
    {
        try {
            // Use cURL to get email activity
            $url = "https://{$this->serverPrefix}.api.mailchimp.com/3.0/reports/{$campaignId}/email-activity";
            $url .= "?count={$limit}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERPWD, "anystring:{$this->apiKey}");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new Exception("API returned HTTP code: {$httpCode}");
            }

            $data = json_decode($response, true);

            return [
                'success' => true,
                'data' => $data,
                'emails' => $data['emails'] ?? [],
                'total_items' => $data['total_items'] ?? 0,
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp get email activity error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'emails' => []
            ];
        }
    }

    /**
     * Sync campaign statistics to local database
     */
    public function syncCampaignStats($newsletter)
    {
        try {
            if (!$newsletter->mailchimp_campaign_id) {
                return [
                    'success' => false,
                    'error' => 'No Mailchimp campaign ID found'
                ];
            }

            // Get campaign email activity
            $activityResult = $this->getCampaignEmailActivity($newsletter->mailchimp_campaign_id);

            if (!$activityResult['success']) {
                return $activityResult;
            }

            $updated = 0;
            foreach ($activityResult['emails'] as $emailActivity) {
                $email = $emailActivity['email_address'];

                // Find the tracking record by email
                $tracking = \App\Models\NewsletterTracking::where('id_newsletter', $newsletter->id_newsletter)
                    ->where('email_tujuan', $email)
                    ->first();

                if (!$tracking) {
                    continue;
                }

                // Count opens and clicks
                $opens = 0;
                $clicks = 0;
                $firstOpenTime = null;
                $firstClickTime = null;

                foreach ($emailActivity['activity'] as $activity) {
                    if ($activity['action'] === 'open') {
                        $opens++;
                        if (!$firstOpenTime) {
                            $firstOpenTime = $activity['timestamp'];
                        }
                    } elseif ($activity['action'] === 'click') {
                        $clicks++;
                        if (!$firstClickTime) {
                            $firstClickTime = $activity['timestamp'];
                        }
                    }
                }

                // Update tracking record
                $tracking->update([
                    'jumlah_dibuka' => $opens,
                    'jumlah_klik' => $clicks,
                    'waktu_dibuka' => $firstOpenTime ? \Carbon\Carbon::parse($firstOpenTime) : null,
                    'waktu_klik' => $firstClickTime ? \Carbon\Carbon::parse($firstClickTime) : null,
                ]);

                $updated++;
            }

            return [
                'success' => true,
                'updated' => $updated
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp sync campaign stats error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Sync all customers to Mailchimp
     */
    public function syncAllCustomers()
    {
        try {
            // Get customers with their user data (where email is stored)
            $pelanggan = \App\Models\Pelanggan::with('user')->get();
            $synced = 0;
            $failed = 0;

            foreach ($pelanggan as $customer) {
                // Skip if customer doesn't have a user account or email
                if (!$customer->user || !$customer->user->email) {
                    $failed++;
                    continue;
                }

                $email = $customer->user->email;
                $names = explode(' ', $customer->nama_pelanggan, 2);
                $firstName = $names[0] ?? '';
                $lastName = $names[1] ?? '';

                $result = $this->addOrUpdateSubscriber(
                    $email,
                    $firstName,
                    $lastName,
                    ['Customer']
                );

                if ($result['success']) {
                    $synced++;
                } else {
                    $failed++;
                }
            }

            return [
                'success' => true,
                'synced' => $synced,
                'failed' => $failed,
                'total' => $pelanggan->count()
            ];
        } catch (Exception $e) {
            Log::error('Mailchimp sync customers error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
