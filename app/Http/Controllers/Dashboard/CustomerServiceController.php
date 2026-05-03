<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\Pelanggan;
use App\Models\Newsletter;
use App\Models\NewsletterTracking;
use App\Services\MailchimpService;
use App\Services\FonnteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerServiceController extends Controller
{
    protected $mailchimp;
    protected $fonnte;

    public function __construct(MailchimpService $mailchimp, FonnteService $fonnte)
    {
        $this->mailchimp = $mailchimp;
        $this->fonnte = $fonnte;
    }

    public function index()
    {
        // Get ticket statistics
        $totalTickets = Ticket::count();
        $pendingTickets = Ticket::whereIn('status', ['open', 'in_progress'])->count();
        $resolvedTickets = Ticket::whereIn('status', ['resolved', 'closed'])->count();

        // Statistik pesan yang ditangani (jumlah TicketMessage)
        $totalMessages = TicketMessage::count();

        // Get ticket statistics per month (last 12 months)
        $ticketsByMonth = Ticket::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as total_count, SUM(CASE WHEN status IN ("resolved", "closed") THEN 1 ELSE 0 END) as resolved_count')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Get ticket messages per month
        $messagesByMonth = TicketMessage::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as message_count')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Format ticket data for chart
        $ticketMonthLabels = [];
        $ticketTotalData = [];
        $ticketResolvedData = [];
        $ticketMessagesData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M Y');

            $ticketMonthLabels[] = $monthLabel;

            $ticketData = $ticketsByMonth->filter(function($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            })->first();

            $messageData = $messagesByMonth->filter(function($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            })->first();

            $ticketTotalData[] = $ticketData ? $ticketData->total_count : 0;
            $ticketResolvedData[] = $ticketData ? $ticketData->resolved_count : 0;
            $ticketMessagesData[] = $messageData ? $messageData->message_count : 0;
        }

        $ticketChartData = [
            'labels' => $ticketMonthLabels,
            'total' => $ticketTotalData,
            'resolved' => $ticketResolvedData,
            'messages' => $ticketMessagesData
        ];

        // Get latest tickets with user relationship
        $latestTickets = Ticket::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Statistik Newsletter
        $totalNewslettersSent = Newsletter::where('status', 'terkirim')->count();
        $totalNewslettersOpened = NewsletterTracking::whereNotNull('waktu_dibuka')->count();

        // Hitung total email promosi yang dikirim (total dari semua newsletter)
        $totalEmailsSent = NewsletterTracking::count();

        // Get campaign statistics per month (last 12 months)
        $campaignsByMonth = Newsletter::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, COUNT(*) as created_count, SUM(CASE WHEN status = "terkirim" THEN 1 ELSE 0 END) as sent_count')
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Format data for chart
        $monthLabels = [];
        $createdData = [];
        $sentData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->format('M Y');

            $monthLabels[] = $monthLabel;

            $monthData = $campaignsByMonth->filter(function($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            })->first();

            $createdData[] = $monthData ? $monthData->created_count : 0;
            $sentData[] = $monthData ? $monthData->sent_count : 0;
        }

        $campaignChartData = [
            'labels' => $monthLabels,
            'created' => $createdData,
            'sent' => $sentData
        ];

        // Get newsletter subscribers from Mailchimp
        Log::info('CS Dashboard: Fetching ALL members from Mailchimp...');
        // Get ALL members regardless of status (subscribed, unsubscribed, cleaned, pending, etc.)
        $mailchimpResult = $this->mailchimp->getListMembers(100, 0, ''); // Empty status = ALL members

        Log::info('CS Dashboard: Mailchimp result', [
            'success' => $mailchimpResult['success'] ?? false,
            'total_items' => $mailchimpResult['total_items'] ?? 0,
            'members_count' => count($mailchimpResult['members'] ?? []),
            'error' => $mailchimpResult['error'] ?? null
        ]);

        if ($mailchimpResult['success']) {
            $subscribers = collect($mailchimpResult['members'])->map(function($member) {
                return (object)[
                    'email' => $member['email_address'] ?? '',
                    'created_at' => $member['timestamp_opt'] ?? $member['timestamp_signup'] ?? null,
                    'status' => $member['status'] ?? 'unknown',
                    'first_name' => $member['merge_fields']['FNAME'] ?? '',
                    'last_name' => $member['merge_fields']['LNAME'] ?? '',
                ];
            });
            $subscribersCount = $mailchimpResult['total_items'] ?? 0;

            Log::info('CS Dashboard: Successfully mapped ' . $subscribers->count() . ' subscribers');
        } else {
            // Fallback to local database if Mailchimp fails
            Log::warning('Failed to fetch Mailchimp subscribers: ' . ($mailchimpResult['error'] ?? 'Unknown error'));

            $subscribers = Pelanggan::with('user')
                ->whereNotNull('id_user')
                ->limit(20)
                ->get()
                ->map(function($pelanggan) {
                    return (object)[
                        'email' => $pelanggan->user->email ?? '',
                        'created_at' => $pelanggan->created_at,
                        'status' => 'subscribed',
                        'first_name' => explode(' ', $pelanggan->nama_pelanggan)[0] ?? '',
                        'last_name' => '',
                    ];
                })
                ->filter(function($item) {
                    return !empty($item->email);
                });

            $subscribersCount = Pelanggan::whereNotNull('id_user')->count();

            Log::info('CS Dashboard: Using fallback data from local database. Count: ' . $subscribersCount);
        }

        // Get Fonnte subscribers from tb_pelanggan table (local database)
        Log::info('CS Dashboard: Fetching Fonnte subscribers from tb_pelanggan table...');

        $fonnteQuery = Pelanggan::query()
            ->with('user')
            ->whereNotNull('no_tlp_pelanggan')
            ->where('no_tlp_pelanggan', '!=', '')
            ->orderBy('id_pelanggan', 'desc')
            ->limit(100);

        $fonnteSubscribers = $fonnteQuery->get()->map(function($pelanggan) {
            return (object)[
                'id' => $pelanggan->id_pelanggan,
                'nome' => $pelanggan->nama_pelanggan ?? '',
                'name' => $pelanggan->nama_pelanggan ?? '',
                'phone' => $pelanggan->no_tlp_pelanggan ?? '',
                'email' => $pelanggan->user->email ?? '',
                'group' => $pelanggan->status_pelanggan ?? '',
                'variable' => [],
                'created_at' => $pelanggan->created_at,
            ];
        });

        $fonnteSubscribersCount = Pelanggan::whereNotNull('no_tlp_pelanggan')
            ->where('no_tlp_pelanggan', '!=', '')
            ->count();

        Log::info('CS Dashboard: Fonnte subscribers fetched from tb_pelanggan. Count: ' . $fonnteSubscribersCount, [
            'contacts_returned' => $fonnteSubscribers->count(),
            'total_in_db' => $fonnteSubscribersCount
        ]);

        return view('cs.dashboard', compact(
            'totalTickets',
            'pendingTickets',
            'resolvedTickets',
            'totalMessages',
            'latestTickets',
            'subscribersCount',
            'subscribers',
            'fonnteSubscribers',
            'fonnteSubscribersCount',
            'totalNewslettersSent',
            'totalNewslettersOpened',
            'totalEmailsSent',
            'campaignChartData',
            'ticketChartData'
        ));
    }

    public function subscribers()
    {
        // Get subscribers from Mailchimp with pagination
        $perPage = 20;
        $page = request()->get('page', 1);
        $offset = ($page - 1) * $perPage;

        $mailchimpResult = $this->mailchimp->getListMembers($perPage, $offset);

        if ($mailchimpResult['success']) {
            $subscribers = collect($mailchimpResult['members'])->map(function($member) {
                return (object)[
                    'email' => $member['email_address'] ?? '',
                    'first_name' => $member['merge_fields']['FNAME'] ?? '',
                    'last_name' => $member['merge_fields']['LNAME'] ?? '',
                    'status' => $member['status'] ?? 'subscribed',
                    'subscribed_at' => $member['timestamp_opt'] ?? null,
                    'last_changed' => $member['last_changed'] ?? null,
                ];
            });

            $total = $mailchimpResult['total_items'] ?? 0;

            // Create manual paginator
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $subscribers,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        } else {
            // Fallback to empty collection if Mailchimp fails
            Log::warning('Failed to fetch Mailchimp subscribers for subscribers page');
            $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                $perPage,
                $page
            );
        }

        return view('cs.subscribers.index', ['subscribers' => $paginator]);
    }

    /**
     * Store a new subscriber to Mailchimp and Fonte
     */
    public function storeSubscriber(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'name' => 'nullable|string|max:255',
        ]);

        try {
            $mailchimpSuccess = false;
            $fonteSuccess = false;
            $errors = [];
            $successMessages = [];

            // Add subscriber to Mailchimp
            Log::info('Adding subscriber to Mailchimp', ['email' => $validated['email']]);
            $mailchimpResult = $this->mailchimp->addOrUpdateSubscriber(
                $validated['email'],
                $validated['first_name'] ?? '',
                $validated['last_name'] ?? '',
                ['Newsletter Subscriber']
            );

            if ($mailchimpResult['success']) {
                $mailchimpSuccess = true;
                $successMessages[] = 'Mailchimp';
                Log::info('Subscriber added to Mailchimp successfully');
            } else {
                $errors[] = 'Mailchimp: ' . ($mailchimpResult['error'] ?? 'Unknown error');
                Log::warning('Failed to add subscriber to Mailchimp', ['error' => $mailchimpResult['error'] ?? 'Unknown']);
            }

            // Add subscriber to Fonnte API if configured
            if ($this->fonnte->isConfigured()) {
                Log::info('Adding subscriber to Fonnte API', ['email' => $validated['email']]);
                $fonteResult = $this->fonnte->addContact(
                    $validated['email'],
                    $validated['name'] ?? ($validated['first_name'] . ' ' . $validated['last_name']),
                    '',
                    []
                );

                if ($fonteResult['success']) {
                    $fonteSuccess = true;
                    $successMessages[] = 'Fonte';
                    Log::info('Subscriber added to Fonnte API successfully');
                } else {
                    $errors[] = 'Fonnte: ' . ($fonteResult['error'] ?? 'Unknown error');
                    Log::warning('Failed to add subscriber to Fonte', ['error' => $fonteResult['error'] ?? 'Unknown']);
                }
            }

            // Determine response based on results
            if ($mailchimpSuccess || $fonteSuccess) {
                $message = 'Subscriber berhasil ditambahkan ke ' . implode(' dan ', $successMessages) . '!';
                if (!empty($errors)) {
                    $message .= ' Namun ada error di sistem lain: ' . implode(', ', $errors);
                }
                return redirect()->route('cs.dashboard')
                    ->with('success', $message . ' Email: ' . $validated['email']);
            } else {
                $allErrors = implode(', ', $errors);
                return redirect()->route('cs.dashboard')
                    ->with('error', 'Gagal menambahkan subscriber: ' . ($allErrors ?: 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error adding subscriber', ['error' => $e->getMessage()]);
            return redirect()->route('cs.dashboard')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove subscriber from Mailchimp and Fonte
     */
    public function destroySubscriber(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'contact_id' => 'nullable|string',
        ]);

        try {
            $mailchimpSuccess = false;
            $fonteSuccess = false;
            $errors = [];
            $successMessages = [];

            // Remove subscriber from Mailchimp
            Log::info('Removing subscriber from Mailchimp', ['email' => $validated['email']]);
            $mailchimpResult = $this->mailchimp->removeSubscriber($validated['email']);

            if ($mailchimpResult['success']) {
                $mailchimpSuccess = true;
                $successMessages[] = 'Mailchimp';
                Log::info('Subscriber removed from Mailchimp successfully');
            } else {
                $errors[] = 'Mailchimp: ' . ($mailchimpResult['error'] ?? 'Unknown error');
                Log::warning('Failed to remove subscriber from Mailchimp', ['error' => $mailchimpResult['error'] ?? 'Unknown']);
            }

            // Remove subscriber from Fonnte API if configured and contact_id provided
            if ($this->fonnte->isConfigured() && !empty($validated['contact_id'])) {
                Log::info('Removing subscriber from Fonnte API', ['contact_id' => $validated['contact_id']]);
                $fonteResult = $this->fonnte->deleteContact($validated['contact_id']);

                if ($fonteResult['success']) {
                    $fonteSuccess = true;
                    $successMessages[] = 'Fonte';
                    Log::info('Subscriber removed from Fonnte API successfully');
                } else {
                    $errors[] = 'Fonnte: ' . ($fonteResult['error'] ?? 'Unknown error');
                    Log::warning('Failed to remove subscriber from Fonte', ['error' => $fonteResult['error'] ?? 'Unknown']);
                }
            }

            // Determine response based on results
            if ($mailchimpSuccess || $fonteSuccess) {
                $message = 'Subscriber berhasil dihapus dari ' . implode(' dan ', $successMessages) . '!';
                if (!empty($errors)) {
                    $message .= ' Namun ada error di sistem lain: ' . implode(', ', $errors);
                }
                return redirect()->route('cs.dashboard')
                    ->with('success', $message);
            } else {
                $allErrors = implode(', ', $errors);
                return redirect()->route('cs.dashboard')
                    ->with('error', 'Gagal menghapus subscriber: ' . ($allErrors ?: 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Error removing subscriber', ['error' => $e->getMessage()]);
            return redirect()->route('cs.dashboard')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Tambah subscriber Fonnte baru (akan menambah ke tabel pelanggan)
     */
    public function storeFonnteSubscriber(Request $request)
    {
        try {
            // Validasi input dasar
            $validated = $request->validate([
                'name' => 'required|string|max:255|min:2',
                'phone' => 'required|string|min:10|max:15',
            ]);

            $name = trim($validated['name']);
            $phone = trim($validated['phone']);

            // Validasi format telepon menggunakan regex dengan error handling
            try {
                $phonePattern = '/^(\+?62|0)[0-9]{9,12}$/';

                // Check if preg_match returns false (error) or 0 (no match)
                $pregResult = preg_match($phonePattern, $phone);

                if ($pregResult === false) {
                    // Error dalam regex - log dan kembalikan pesan error yang helpful
                    $pregError = preg_last_error_msg();
                    Log::error('Fonnte Subscriber: Regex error', [
                        'error_code' => preg_last_error(),
                        'error_msg' => $pregError,
                        'pattern' => $phonePattern,
                        'phone' => $phone
                    ]);

                    return redirect()->route('cs.dashboard')
                        ->withErrors(['phone' => 'Terjadi kesalahan pada validasi nomor telepon. Silakan hubungi admin.'])
                        ->withInput();
                }

                if ($pregResult === 0) {
                    // No match - format telepon salah
                    return redirect()->route('cs.dashboard')
                        ->withErrors(['phone' => 'Format nomor telepon tidak valid. Gunakan format: 62812345678, +62812345678, atau 08123456789'])
                        ->withInput();
                }

            } catch (\Exception $regexException) {
                Log::error('Fonnte Subscriber: Exception during regex validation', [
                    'error' => $regexException->getMessage()
                ]);

                return redirect()->route('cs.dashboard')
                    ->withErrors(['phone' => 'Terjadi kesalahan validasi. Silakan coba lagi.'])
                    ->withInput();
            }

            // Cek apakah nomor telepon sudah ada di tabel pelanggan
            $existingPelanggan = Pelanggan::where('no_tlp_pelanggan', $phone)->first();
            if ($existingPelanggan) {
                return redirect()->route('cs.dashboard')
                    ->with('error', 'Nomor telepon ini sudah terdaftar dalam sistem.');
            }

            // Tambahkan ke tabel pelanggan dengan id_user kosong dan tanpa alamat
            Log::info('Adding Fonnte subscriber to pelanggan table', ['phone' => $phone, 'name' => $name]);

            $pelanggan = Pelanggan::create([
                'nama_pelanggan' => $name,
                'no_tlp_pelanggan' => $phone,
                'id_user' => null,  // Kosongkan id_user
                'status_pelanggan' => 'aktif',
            ]);

            if ($pelanggan) {
                Log::info('Fonnte subscriber added successfully to pelanggan table', [
                    'id_pelanggan' => $pelanggan->id_pelanggan,
                    'name' => $name,
                    'phone' => $phone
                ]);

                return redirect()->route('cs.dashboard')
                    ->with('success', 'Fonnte subscriber berhasil ditambahkan! Nama: ' . $name . ', Nomor: ' . $phone);
            } else {
                Log::warning('Failed to add Fonnte subscriber to pelanggan table', ['name' => $name, 'phone' => $phone]);

                return redirect()->route('cs.dashboard')
                    ->with('error', 'Gagal menambahkan Fonnte subscriber.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Laravel validation error
            Log::warning('Fonnte Subscriber: Validation error', ['errors' => $e->errors()]);
            return redirect()->route('cs.dashboard')
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Exception $e) {
            Log::error('Fonnte Subscriber: Unexpected error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('cs.dashboard')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
