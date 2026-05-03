<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FonnteService;
use App\Models\FonnteContact;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DebugFonnteNewsletter extends Command
{
    protected $signature = 'fonnte:debug-newsletter {newsletter_id?} {--test-send} {--check-api} {--list-contacts} {--send-test-message}';
    protected $description = 'Debug Fonnte newsletter delivery issues';

    public function __construct(protected FonnteService $fonte)
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->line('         FONTE NEWSLETTER DEBUG TOOL');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->line('');

        // Check API Configuration
        if ($this->option('check-api')) {
            $this->checkApiConfiguration();
        }

        // List Contacts
        if ($this->option('list-contacts')) {
            $this->listContacts();
        }

        // Send Test Message
        if ($this->option('send-test-message')) {
            $this->sendTestMessage();
        }

        // Test Send Newsletter
        if ($this->option('test-send')) {
            $this->testSendNewsletter();
        }

        // Debug Specific Newsletter
        if ($this->argument('newsletter_id')) {
            $this->debugNewsletter($this->argument('newsletter_id'));
        }

        if (!$this->option('check-api') && !$this->option('list-contacts') && !$this->option('send-test-message') && !$this->option('test-send') && !$this->argument('newsletter_id')) {
            $this->showHelp();
        }

        $this->line('');
        $this->line('═══════════════════════════════════════════════════════════════');
        $this->line('');
    }

    private function checkApiConfiguration()
    {
        $this->line('🔍 <fg=cyan>Checking API Configuration...</fg=cyan>');
        $this->line('');

        $apiKey = config('services.fonnte.api_key');
        $baseUrl = config('services.fonnte.base_url');

        $this->table(['Setting', 'Value', 'Status'], [
            ['FONNTE_API_KEY', $apiKey ? '***' . substr($apiKey, -10) : 'NOT SET', $apiKey ? '✅' : '❌'],
            ['FONNTE_BASE_URL', $baseUrl ?? 'NOT SET', $baseUrl ? '✅' : '❌'],
            ['Device Token', config('services.fonnte.device_token') ?? 'NOT SET', config('services.fonnte.device_token') ? '✅' : '❌'],
        ]);

        if (!$apiKey || !$baseUrl) {
            $this->error('❌ Fonnte API is not properly configured!');
            $this->line('Please add these to your .env file:');
            $this->line('  FONTE_API_KEY=your_api_key_here');
            $this->line('  FONTE_BASE_URL=https://api.fonnte.com');
            $this->line('');
            return;
        }

        $this->info('✅ API Configuration looks good!');
        $this->line('');

        // Test API Connection
        $this->line('🔗 Testing Fonnte API Connection...');
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Accept' => 'application/json',
            ])->timeout(10)->get($baseUrl);

            $statusCode = $response['status'] ?? 200;
            $this->info('✅ Successfully connected to Fonnte API!');
            $this->line('   Connection successful');
        } catch (\Exception $e) {
            $this->error('❌ Failed to connect to Fonnte API!');
            $this->line('   Error: ' . $e->getMessage());
        }
        $this->line('');
    }

    private function listContacts()
    {
        $this->line('📋 <fg=cyan>Listing Fonnte Contacts...</fg=cyan>');
        $this->line('');

        $result = $this->fonte->getContacts(100, 0);

        if (!$result['success']) {
            $this->error('❌ Failed to fetch contacts: ' . $result['error']);
            return;
        }

        $contacts = $result['contacts'] ?? [];
        $total = $result['total_items'] ?? 0;

        $this->info("✅ Found {$total} contacts");
        $this->line('');

        if (count($contacts) === 0) {
            $this->warn('⚠️ No contacts found in database!');
            return;
        }

        $tableData = [];
        foreach (array_slice($contacts, 0, 20) as $contact) {
            $tableData[] = [
                $contact['nome'] ?? '-',
                $contact['phone'] ?? '-',
                $contact['email'] ?? '-',
                \Carbon\Carbon::parse($contact['created_at'] ?? now())->format('d M Y'),
            ];
        }

        $this->table(['Name', 'Phone', 'Email', 'Created'], $tableData);

        if (count($contacts) > 20) {
            $this->line('... and ' . (count($contacts) - 20) . ' more contacts');
        }

        $this->line('');
    }

    private function sendTestMessage()
    {
        $this->line('📨 <fg=cyan>Send Test Message</fg=cyan>');
        $this->line('');

        $phone = $this->ask('Enter phone number (format: 0812345678 or 62812345678)');
        $message = $this->ask('Enter message text (or press Enter for default)') ?: 'Test message from Fonnte API debugging tool';

        if (!$phone) {
            $this->error('❌ Phone number is required!');
            return;
        }

        $this->line('');
        $this->info('📤 Sending message...');
        $this->line('   Phone: ' . $phone);
        $this->line('   Message: ' . $message);
        $this->line('');

        $result = $this->fonte->sendMessage($phone, $message, 'text');

        if ($result['success']) {
            $this->info('✅ Message sent successfully!');
            $this->line('Response data:');
            $this->line(json_encode($result['data'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        } else {
            $this->error('❌ Failed to send message!');
            $this->line('Error: ' . $result['error']);
        }

        $this->line('');
        $this->warn('📝 Check WhatsApp on the recipient phone for the message.');
        $this->line('');
    }

    private function testSendNewsletter()
    {
        $this->line('📬 <fg=cyan>Test Newsletter Send</fg=cyan>');
        $this->line('');

        $subject = $this->ask('Enter subject/title') ?: 'Test Newsletter';
        $message = $this->ask('Enter message body (or press Enter for default)') ?: 'This is a test message from Fonnte API debugging tool.';

        $this->line('');
        $this->info('📤 Sending newsletter...');

        $result = $this->fonte->sendNewsletter($subject, $message, 'text');

        if ($result['success']) {
            $this->info('✅ Newsletter send attempt completed!');
            $this->table(['Metric', 'Value'], [
                ['Total Contacts', $result['total_contacts'] ?? 0],
                ['Total Sent', $result['total_sent'] ?? 0],
                ['Total Failed', $result['total_failed'] ?? 0],
            ]);

            if (isset($result['messages']) && count($result['messages']) > 0) {
                $this->line('');
                $this->line('📋 Send Results Summary:');
                $messageData = [];
                foreach (array_slice($result['messages'], 0, 10) as $msg) {
                    $messageData[] = [
                        $msg['name'] ?? '-',
                        $msg['phone'] ?? '-',
                        $msg['success'] ? '✅' : '❌',
                        $msg['error'] ?? '-',
                    ];
                }
                $this->table(['Name', 'Phone', 'Status', 'Error/Note'], $messageData);

                if (count($result['messages']) > 10) {
                    $this->line('... and ' . (count($result['messages']) - 10) . ' more');
                }
            }
        } else {
            $this->error('❌ Failed to send newsletter!');
            $this->line('Error: ' . $result['error']);
        }

        $this->line('');
        $this->warn('📝 Check WhatsApp messages on registered phones.');
        $this->line('');
    }

    private function debugNewsletter($newsletterId)
    {
        $this->line('🔎 <fg=cyan>Debugging Newsletter #' . $newsletterId . '</fg=cyan>');
        $this->line('');

        $newsletter = Newsletter::find($newsletterId);

        if (!$newsletter) {
            $this->error('❌ Newsletter not found!');
            return;
        }

        // Newsletter Info
        $this->table(['Field', 'Value'], [
            ['ID', $newsletter->id_newsletter],
            ['Title', $newsletter->judul],
            ['Subject', $newsletter->subjek_email],
            ['Delivery Method', $newsletter->metode_pengiriman],
            ['Status', $newsletter->status],
            ['Sent Date', $newsletter->tanggal_kirim ? $newsletter->tanggal_kirim->format('d M Y H:i:s') : 'Not sent'],
            ['Total Recipients', $newsletter->total_penerima ?? 0],
            ['Total Sent', $newsletter->total_terkirim ?? 0],
            ['Total Failed', $newsletter->total_gagal ?? 0],
        ]);

        $this->line('');
        $this->line('📝 <fg=yellow>Message Content:</fg=yellow>');
        $this->line('');
        $this->line(wordwrap($newsletter->konten_email, 80));

        $this->line('');
        $this->line('📊 <fg=yellow>Tracking Data:</fg=yellow>');
        $trackings = $newsletter->trackings()->take(10)->get();
        if ($trackings->count() > 0) {
            $trackingData = [];
            foreach ($trackings as $tracking) {
                $trackingData[] = [
                    $tracking->pelanggan->nama_pelanggan ?? '-',
                    $tracking->email_tujuan ?? '-',
                    $tracking->status_kirim ?? 'pending',
                    $tracking->tanggal_kirim ? $tracking->tanggal_kirim->format('d M Y H:i') : '-',
                ];
            }
            $this->table(['Customer', 'Email', 'Status', 'Date'], $trackingData);
        } else {
            $this->warn('⚠️ No tracking records found');
        }

        $this->line('');
        $this->line('📜 <fg=yellow>Recent Logs:</fg=yellow>');
        $this->line('');

        // Check recent logs
        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            $logs = shell_exec("grep -i 'fonte' {$logPath} | tail -30");
            if ($logs) {
                $this->line($logs);
            } else {
                $this->warn('⚠️ No Fonnte logs found in recent entries');
            }
        }

        $this->line('');
    }

    private function showHelp()
    {
        $this->line('📚 <fg=yellow>Usage Examples:</fg=yellow>');
        $this->line('');
        $this->line('1. <fg=cyan>Check API Configuration:</fg=cyan>');
        $this->line('   php artisan fonte:debug-newsletter --check-api');
        $this->line('');
        $this->line('2. <fg=cyan>List All Contacts:</fg=cyan>');
        $this->line('   php artisan fonte:debug-newsletter --list-contacts');
        $this->line('');
        $this->line('3. <fg=cyan>Send Test Message:</fg=cyan>');
        $this->line('   php artisan fonte:debug-newsletter --send-test-message');
        $this->line('');
        $this->line('4. <fg=cyan>Test Newsletter Send:</fg=cyan>');
        $this->line('   php artisan fonte:debug-newsletter --test-send');
        $this->line('');
        $this->line('5. <fg=cyan>Debug Specific Newsletter:</fg=cyan>');
        $this->line('   php artisan fonte:debug-newsletter 5');
        $this->line('');
    }
}
