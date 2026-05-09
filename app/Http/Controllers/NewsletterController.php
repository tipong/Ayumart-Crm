<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Newsletter;
use App\Models\NewsletterTracking;
use App\Models\Pelanggan;
use App\Services\MailchimpService;
use App\Services\FonnteService;

class NewsletterController extends Controller
{
    protected $mailchimp;
    protected $fonnte;

    public function __construct(MailchimpService $mailchimp, FonnteService $fonnte)
    {
        $this->mailchimp = $mailchimp;
        $this->fonnte = $fonnte;
    }

    /**
     * Display a listing of newsletters
     */
    public function index()
    {
        $newsletters = Newsletter::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('cs.newsletters.index', compact('newsletters'));
    }

    /**
     * Show the form for creating a new newsletter
     */
    public function create()
    {
        // Get total subscribers count from Mailchimp
        $mailchimpCount = $this->mailchimp->getListMembersCount();
        $subscribersCount = $mailchimpCount['success'] ? $mailchimpCount['count'] : 0;

        // Get Fonnte subscribers count from tb_pelanggan (customers with phone numbers)
        $fonteCount = Pelanggan::whereNotNull('no_tlp_pelanggan')
            ->where('no_tlp_pelanggan', '!=', '')
            ->count();

        // Also get local count
        $localCount = DB::table('tb_pelanggan')->count();

        // Get subscriber counts by membership tier for filtering
        $membershipTiers = \App\Models\Membership::getTierCounts();

        // Get available tiers for filtering
        $availableTiers = [
            'bronze' => ['name' => 'Bronze', 'label' => 'Bronze (0-100 poin)', 'count' => $membershipTiers['bronze'] ?? 0],
            'silver' => ['name' => 'Silver', 'label' => 'Silver (101-250 poin)', 'count' => $membershipTiers['silver'] ?? 0],
            'gold' => ['name' => 'Gold', 'label' => 'Gold (251-400 poin)', 'count' => $membershipTiers['gold'] ?? 0],
            'platinum' => ['name' => 'Platinum', 'label' => 'Platinum (401+ poin)', 'count' => $membershipTiers['platinum'] ?? 0],
        ];

        return view('cs.newsletters.create', compact('subscribersCount', 'fonteCount', 'localCount', 'availableTiers'));
    }

    /**
     * Store a newly created newsletter
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'subjek_email' => 'required|string|max:200',
            'jenis_newsletter' => 'required|in:mailchimp,fonnte,keduanya',
            'konten_email' => 'required|string',
            'konten_html' => 'nullable|string',
            'target_tiers' => 'nullable|array',
            'target_tiers.*' => 'in:bronze,silver,gold,platinum',
        ]);

        // Map jenis_newsletter to metode_pengiriman for database compatibility
        $validated['metode_pengiriman'] = $validated['jenis_newsletter'];
        $validated['dibuat_oleh'] = Auth::user()->id_user;
        $validated['status'] = 'draft';

        // Store target tiers as JSON if provided
        if (!empty($validated['target_tiers'])) {
            $validated['target_tiers'] = json_encode($validated['target_tiers']);
        } else {
            $validated['target_tiers'] = null;
        }

        $newsletter = Newsletter::create($validated);

        return redirect()->route('cs.newsletters.show', $newsletter->id_newsletter)
            ->with('success', 'Newsletter berhasil dibuat sebagai draft');
    }

    /**
     * Display the specified newsletter
     */
    public function show($id)
    {
        $newsletter = Newsletter::with(['creator', 'trackings.pelanggan.user'])->findOrFail($id);

        $mailchimpStats = null;
        $mailchimpActivity = null;

        // Calculate local statistics
        $totalSent = $newsletter->trackings->where('status_kirim', 'terkirim')->count();
        $totalOpened = $newsletter->trackings->whereNotNull('waktu_dibuka')->count();
        $totalClicked = $newsletter->trackings->whereNotNull('waktu_klik')->count();
        $openRate = $totalSent > 0 ? ($totalOpened / $totalSent) * 100 : 0;
        $clickRate = $totalSent > 0 ? ($totalClicked / $totalSent) * 100 : 0;

        // If newsletter is sent and has a Mailchimp campaign ID, get stats from Mailchimp
        if ($newsletter->status === 'terkirim' && $newsletter->mailchimp_campaign_id) {
            // Sync campaign stats to local database
            $syncResult = $this->mailchimp->syncCampaignStats($newsletter);

            if ($syncResult['success']) {
                // Reload the newsletter to get updated tracking data
                $newsletter = Newsletter::with(['creator', 'trackings.pelanggan.user'])->findOrFail($id);

                // Recalculate local statistics after sync
                $totalSent = $newsletter->trackings->where('status_kirim', 'terkirim')->count();
                $totalOpened = $newsletter->trackings->whereNotNull('waktu_dibuka')->count();
                $totalClicked = $newsletter->trackings->whereNotNull('waktu_klik')->count();
                $openRate = $totalSent > 0 ? ($totalOpened / $totalSent) * 100 : 0;
                $clickRate = $totalSent > 0 ? ($totalClicked / $totalSent) * 100 : 0;
            }

            // Get campaign report from Mailchimp for display
            $reportResult = $this->mailchimp->getCampaignReport($newsletter->mailchimp_campaign_id);

            if ($reportResult['success']) {
                $mailchimpStats = [
                    'emails_sent' => $reportResult['emails_sent'] ?? 0,
                    'unique_opens' => $reportResult['opens'] ?? 0,
                    'unique_clicks' => $reportResult['clicks'] ?? 0,
                    'open_rate' => $reportResult['open_rate'] ?? 0,
                    'click_rate' => $reportResult['click_rate'] ?? 0,
                ];
            }

            // Get detailed email activity from Mailchimp
            $activityResult = $this->mailchimp->getCampaignEmailActivity($newsletter->mailchimp_campaign_id);

            if ($activityResult['success']) {
                $mailchimpActivity = collect($activityResult['emails'])->map(function($email) {
                    $activities = collect($email['activity'] ?? []);

                    return [
                        'email' => $email['email_address'],
                        'email_id' => $email['email_id'] ?? null,
                        'status' => $email['activity'] ? 'sent' : 'pending',
                        'first_open' => $activities->where('action', 'open')->first()['timestamp'] ?? null,
                        'first_click' => $activities->where('action', 'click')->first()['timestamp'] ?? null,
                        'open_count' => $activities->where('action', 'open')->count(),
                        'click_count' => $activities->where('action', 'click')->count(),
                    ];
                });
            }
        }

        return view('cs.newsletters.show', compact(
            'newsletter',
            'mailchimpStats',
            'mailchimpActivity',
            'totalSent',
            'totalOpened',
            'totalClicked',
            'openRate',
            'clickRate'
        ));
    }

    /**
     * Show the form for editing the newsletter
     */
    public function edit($id)
    {
        $newsletter = Newsletter::findOrFail($id);

        if ($newsletter->isSent()) {
            return redirect()->route('cs.newsletters.show', $id)
                ->with('error', 'Newsletter yang sudah terkirim tidak dapat diedit');
        }

        return view('cs.newsletters.edit', compact('newsletter'));
    }

    /**
     * Update the specified newsletter
     */
    public function update(Request $request, $id)
    {
        $newsletter = Newsletter::findOrFail($id);

        if ($newsletter->isSent()) {
            return redirect()->route('cs.newsletters.show', $id)
                ->with('error', 'Newsletter yang sudah terkirim tidak dapat diedit');
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'subjek_email' => 'required|string|max:200',
            'jenis_newsletter' => 'required|in:mailchimp,fonnte,keduanya',
            'konten_email' => 'required|string',
            'konten_html' => 'nullable|string',
        ]);

        $newsletter->update($validated);

        return redirect()->route('cs.newsletters.show', $id)
            ->with('success', 'Newsletter berhasil diperbarui');
    }

    /**
     * Remove the specified newsletter
     */
    public function destroy($id)
    {
        $newsletter = Newsletter::findOrFail($id);

        if ($newsletter->isSent()) {
            return redirect()->route('cs.newsletters.index')
                ->with('error', 'Newsletter yang sudah terkirim tidak dapat dihapus');
        }

        $newsletter->delete();

        return redirect()->route('cs.newsletters.index')
            ->with('success', 'Newsletter berhasil dihapus');
    }

    /**
     * Send newsletter via Mailchimp and/or Fonte
     */
    public function send(Request $request, $id)
    {
        $newsletter = Newsletter::findOrFail($id);

        if ($newsletter->isSent()) {
            return back()->with('error', 'Newsletter sudah pernah dikirim');
        }

        try {
            DB::beginTransaction();

            // Update status to sending
            $newsletter->update(['status' => 'mengirim']);

            $totalMailchimp = 0;
            $totalFonnte = 0;
            $totalFonnteFailed = 0;
            $fonteResult = [];
            $metode = $newsletter->metode_pengiriman;

            // ========== MAILCHIMP DELIVERY ==========
            if ($metode === 'mailchimp' || $metode === 'keduanya') {
                // Step 1: Sync all customers to Mailchimp (if needed)
                if ($request->has('sync_customers')) {
                    $syncResult = $this->mailchimp->syncAllCustomers();
                    Log::info('Mailchimp sync result: ' . json_encode($syncResult));
                }

                // Step 2: Create campaign in Mailchimp
                $campaignResult = $this->mailchimp->createCampaign(
                    $newsletter->subjek_email,
                    $newsletter->judul
                );

                if (!$campaignResult['success']) {
                    throw new \Exception('Gagal membuat campaign di Mailchimp: ' . $campaignResult['error']);
                }

                $campaignId = $campaignResult['campaign_id'];

                // Step 3: Set campaign content
                $htmlContent = $newsletter->konten_html ?: $this->convertToHtml($newsletter->konten_email);

                $contentResult = $this->mailchimp->setCampaignContent(
                    $campaignId,
                    $htmlContent,
                    $newsletter->konten_email
                );

                if (!$contentResult['success']) {
                    throw new \Exception('Gagal mengatur konten campaign: ' . $contentResult['error']);
                }

                // Step 4: Send campaign
                $sendResult = $this->mailchimp->sendCampaign($campaignId);

                if (!$sendResult['success']) {
                    throw new \Exception('Gagal mengirim campaign: ' . $sendResult['error']);
                }

                // Step 5: Get subscriber count
                $countResult = $this->mailchimp->getListMembersCount();
                $totalMailchimp = $countResult['success'] ? $countResult['count'] : 0;

                // Step 6: Store campaign ID
                $newsletter->update(['mailchimp_campaign_id' => $campaignId]);

                // Step 7: Create tracking records for local database
                $pelanggan = Pelanggan::with('user')->get();
                foreach ($pelanggan as $customer) {
                    // Skip if customer doesn't have a user account or email
                    if (!$customer->user || !$customer->user->email) {
                        continue;
                    }

                    $email = $customer->user->email;

                    NewsletterTracking::create([
                        'id_newsletter' => $newsletter->id_newsletter,
                        'id_pelanggan' => $customer->id_pelanggan,
                        'email_tujuan' => $email,
                        'subjek_email' => $newsletter->subjek_email,
                        'konten_email' => $newsletter->konten_email,
                        'tanggal_kirim' => now(),
                        'status_kirim' => 'terkirim',
                        'mailchimp_member_id' => md5(strtolower($email)),
                    ]);
                }

                Log::info('Newsletter sent via Mailchimp', [
                    'newsletter_id' => $id,
                    'total_recipients' => $totalMailchimp,
                    'campaign_id' => $campaignId
                ]);
            }

            // ========== FONTE DELIVERY (WhatsApp) ==========
            if ($metode === 'fonnte' || $metode === 'keduanya') {
                if (!$this->fonnte->isConfigured()) {
                    throw new \Exception('Fonnte API tidak terkonfigurasi. Silakan atur FONTE_API_KEY dan FONTE_BASE_URL di .env');
                }

                // Send newsletter via Fonnte (WhatsApp) using customers from tb_pelanggan
                $fonteResult = $this->sendNewsletterViaFonnte(
                    $newsletter,
                    $id
                );

                if (!$fonteResult['success']) {
                    throw new \Exception('Gagal mengirim via Fonnte: ' . $fonteResult['error']);
                }

                // IMPORTANT: Use total_contacts (intended recipients) not total_sent (successfully delivered)
                // This is because we need to track who SHOULD have received it
                // total_sent shows actual delivery, but if token is invalid, 0 gets stored incorrectly
                $totalFonnte = $fonteResult['total_contacts'] ?? 0;
                $totalFonnteFailed = $fonteResult['total_failed'] ?? 0;

                Log::info('Newsletter sent via Fonnte (WhatsApp)', [
                    'newsletter_id' => $id,
                    'total_contacts' => $totalFonnte,
                    'total_sent' => $fonteResult['total_sent'] ?? 0,
                    'total_failed' => $totalFonnteFailed,
                    'message' => 'Using customers from tb_pelanggan with phone numbers'
                ]);
            }

            // Calculate total recipients
            $totalPenerima = $totalMailchimp + $totalFonnte;
            // For terkirim, only count successfully sent Mailchimp + successfully sent Fonte
            $totalTerkirim = $totalMailchimp + ($fonteResult['total_sent'] ?? 0);
            // For gagal, count only Fonte failures (Mailchimp success is implicit)
            $totalGagal = 0 + $totalFonnteFailed;

            // Step 8: Update newsletter with delivery results
            $newsletter->update([
                'status' => 'terkirim',
                'tanggal_kirim' => now(),
                'total_penerima' => $totalPenerima,
                'total_terkirim' => $totalTerkirim,
                'total_gagal' => $totalGagal,
            ]);

            DB::commit();

            // Build success message
            $message = 'Newsletter berhasil dikirim';
            if ($metode === 'mailchimp') {
                $message .= " ke {$totalMailchimp} subscribers via Mailchimp";
            } elseif ($metode === 'fonnte') {
                $message .= " ke {$totalFonnte} subscribers via Fonnte (WhatsApp)";
            } else {
                $message .= " ke {$totalMailchimp} subscribers via Mailchimp dan {$totalFonnte} via Fonnte";
            }
            $message .= "!";

            return redirect()->route('cs.newsletters.show', $id)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            // Update newsletter status to failed
            $newsletter->update(['status' => 'gagal']);

            Log::error('Newsletter send error', [
                'newsletter_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Gagal mengirim newsletter: ' . $e->getMessage());
        }
    }

    /**
     * Convert plain text to HTML
     */
    private function convertToHtml($text)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #0066cc; }
        p { margin-bottom: 15px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="content">';

        $html .= nl2br(e($text));

        $html .= '</div>
    <div class="footer">
        <p>© ' . now()->year . ' AyuMart. All rights reserved.</p>
    </div>
</body>
</html>';

        return $html;
    }

    /**
     * Subscribe to newsletter (public)
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'nama' => 'nullable|string|max:100',
        ]);

        try {
            $names = explode(' ', $validated['nama'] ?? '', 2);
            $firstName = $names[0] ?? '';
            $lastName = $names[1] ?? '';

            $result = $this->mailchimp->addOrUpdateSubscriber(
                $validated['email'],
                $firstName,
                $lastName,
                ['Website Subscriber']
            );

            if ($result['success']) {
                return back()->with('success', 'Terima kasih! Anda telah berlangganan newsletter kami.');
            } else {
                throw new \Exception($result['error']);
            }
        } catch (\Exception $e) {
            Log::error('Newsletter subscribe error: ' . $e->getMessage());
            return back()->with('error', 'Gagal berlangganan newsletter. Silakan coba lagi.');
        }
    }

    /**
     * Unsubscribe from newsletter (public)
     */
    public function unsubscribe($token)
    {
        // Token is the email address encoded
        $email = base64_decode($token);

        try {
            $result = $this->mailchimp->removeSubscriber($email);

            if ($result['success']) {
                return view('newsletters.unsubscribed')->with('success', 'Anda telah berhenti berlangganan newsletter kami.');
            } else {
                throw new \Exception($result['error']);
            }
        } catch (\Exception $e) {
            Log::error('Newsletter unsubscribe error: ' . $e->getMessage());
            return view('newsletters.unsubscribed')->with('error', 'Gagal berhenti berlangganan. Silakan hubungi kami.');
        }
    }

    /**
     * Send newsletter via Fonnte using customers from tb_pelanggan
     * This method gets phone numbers from tb_pelanggan table instead of FonnteContact
     *
     * @param Newsletter $newsletter
     * @param int $id Newsletter ID
     * @return array
     */
    private function sendNewsletterViaFonnte($newsletter, $id)
    {
        try {
            // Get target tiers if any
            $targetTiers = null;
            if (!empty($newsletter->target_tiers)) {
                $targetTiers = is_string($newsletter->target_tiers) ? json_decode($newsletter->target_tiers, true) : $newsletter->target_tiers;
            }

            // Get customers with phone numbers from tb_pelanggan, filtered by tier if applicable
            $customersResult = $this->fonnte->getCustomersWithPhones(PHP_INT_MAX, 0, $targetTiers);

            if (!$customersResult['success']) {
                throw new \Exception('Gagal mengambil data pelanggan: ' . $customersResult['error']);
            }

            $customers = $customersResult['contacts'] ?? [];
            $totalContacts = count($customers);

            if ($totalContacts === 0) {
                Log::warning('Fonnte: No customers with phone numbers found for newsletter sending', [
                    'newsletter_id' => $id
                ]);

                return [
                    'success' => true,
                    'message' => 'No customers with phone numbers to send newsletter to',
                    'total_sent' => 0,
                    'total_failed' => 0,
                    'total_contacts' => 0
                ];
            }

            // Prepare batch data for Fonnte API
            $batchData = [];
            $messages = [];
            $totalProcessed = 0;

            $formattedMessage = "📬 *{$newsletter->judul}*\n\n{$newsletter->konten_email}";

            foreach ($customers as $customer) {
                try {
                    if (empty($customer['phone'])) {
                        $messages[] = [
                            'phone' => $customer['phone'] ?? 'N/A',
                            'name' => $customer['nome'] ?? 'Unknown',
                            'success' => false,
                            'error' => 'No phone number'
                        ];
                        continue;
                    }

                    $normalizedPhone = $this->fonnte->normalizePhoneNumber($customer['phone']);

                    // Add to batch data
                    $batchData[] = [
                        'target' => $normalizedPhone,
                        'message' => $formattedMessage,
                        'delay' => '1'  // 1 second delay between messages
                    ];

                    $totalProcessed++;

                    // Create tracking record for local database
                    NewsletterTracking::create([
                        'id_newsletter' => $newsletter->id_newsletter,
                        'id_pelanggan' => $customer['id'] ?? null,
                        'email_tujuan' => $customer['email'] ?? '',
                        'subjek_email' => $newsletter->judul,
                        'konten_email' => $newsletter->konten_email,
                        'tanggal_kirim' => now(),
                        'status_kirim' => 'terkirim',
                        'phone' => $normalizedPhone,
                    ]);

                    $messages[] = [
                        'phone' => $normalizedPhone,
                        'name' => $customer['nome'] ?? 'Unknown',
                        'queued' => true
                    ];
                } catch (\Exception $e) {
                    Log::error('Error processing customer for newsletter', [
                        'customer_id' => $customer['id'] ?? null,
                        'phone' => $customer['phone'] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ]);

                    $messages[] = [
                        'phone' => $customer['phone'] ?? 'Unknown',
                        'name' => $customer['nome'] ?? 'Unknown',
                        'success' => false,
                        'error' => $e->getMessage()
                    ];
                }
            }

            // If no valid contacts, return error
            if (empty($batchData)) {
                Log::warning('Fonnte: No valid customers with phone numbers to send', [
                    'newsletter_id' => $id,
                    'total_customers' => $totalContacts,
                    'valid_customers' => 0
                ]);

                return [
                    'success' => false,
                    'error' => 'No customers with valid phone numbers',
                    'total_sent' => 0,
                    'total_failed' => $totalContacts,
                    'total_contacts' => $totalContacts,
                    'messages' => $messages
                ];
            }

            // Send batch using Fonnte API
            $fonteResponse = $this->fonnte->sendBatchMessages($batchData);

            if (!$fonteResponse['success']) {
                Log::error('Fonnte: Batch sending failed', [
                    'newsletter_id' => $id,
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

            Log::info('Fonnte: Newsletter batch sent successfully from tb_pelanggan', [
                'newsletter_id' => $id,
                'total_customers' => $totalContacts,
                'total_queued' => $totalSent,
                'total_failed_validation' => $totalFailed,
                'subject' => $newsletter->judul,
                'source' => 'tb_pelanggan'
            ]);

            return [
                'success' => true,
                'message' => "Newsletter queued to {$totalSent}/{$totalContacts} customers",
                'total_sent' => $totalSent,
                'total_failed' => $totalFailed,
                'total_contacts' => $totalContacts,
                'messages' => $messages,
                'fonnte_response' => $fonteResponse['data'] ?? null
            ];
        } catch (\Exception $e) {
            Log::error('Exception sending newsletter via Fonnte from tb_pelanggan', [
                'newsletter_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'total_sent' => 0,
                'total_failed' => 0,
                'total_contacts' => 0
            ];
        }
    }
}
