<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FonnteService;
use App\Models\FonnteContact;
use Illuminate\Support\Facades\Log;

class TestFonnteSending extends Command
{
    protected $signature = 'fonte:test-send {phone?} {message?}';
    protected $description = 'Test sending WhatsApp message via Fonnte';

    public function handle()
    {
        $this->line('╔════════════════════════════════════════════════════════╗');
        $this->line('║         FONTE WHATSAPP MESSAGE TEST UTILITY            ║');
        $this->line('╚════════════════════════════════════════════════════════╝');
        $this->line('');

        $fonte = app(FonnteService::class);

        // Check configuration
        $this->info('1. CHECKING CONFIGURATION');
        $this->line('   API Key configured: ' . ($fonte->isConfigured() ? '✓ YES' : '✗ NO'));

        if (!$fonte->isConfigured()) {
            $this->error('   ✗ Fonnte is not configured. Set FONTE_API_KEY and FONTE_BASE_URL in .env');
            return;
        }

        // Get contacts from database
        $this->line('');
        $this->info('2. CHECKING CONTACTS IN DATABASE');
        $contacts = FonnteContact::all();
        $this->line('   Total contacts: ' . $contacts->count());

        if ($contacts->count() > 0) {
            $this->table(['ID', 'Name', 'Phone', 'Email'], $contacts->map(function($c) {
                return [$c->id, $c->nome, $c->phone, $c->email];
            })->toArray());
        }

        // Test sending
        $this->line('');
        $this->info('3. SENDING TEST MESSAGE');

        $phone = $this->argument('phone');
        $message = $this->argument('message');

        if (!$phone) {
            if ($contacts->count() === 0) {
                $this->error('   ✗ No contacts in database. Add a contact first.');
                return;
            }
            $phone = $contacts->first()->phone;
            $this->line('   Using first contact phone: ' . $phone);
        }

        if (!$message) {
            $message = 'This is a test message from the Fonnte integration at ' . now()->format('Y-m-d H:i:s');
            $this->line('   Using default message');
        }

        $this->line('   Phone: ' . $phone);
        $this->line('   Message: ' . $message);
        $this->line('');
        $this->line('   Sending...');

        $result = $fonte->sendMessage($phone, $message, 'text');

        $this->line('');
        $this->info('4. RESULT');

        if ($result['success']) {
            $this->line('   ✓ SUCCESS - Message sent successfully!');
            $this->line('   Response: ' . json_encode($result['data'] ?? []));
        } else {
            $this->error('   ✗ FAILED - ' . ($result['error'] ?? 'Unknown error'));
        }

        $this->line('');
        $this->line('5. DEBUGGING INFO');
        $this->line('   Check logs at: storage/logs/laravel.log');
        $this->line('   Latest logs:');
        $this->line('');

        // Show last 10 lines of log
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $lines = array_slice(file($logFile), -10);
            foreach ($lines as $line) {
                if (strpos($line, 'Fonnte:') !== false) {
                    $this->line('   ' . trim($line));
                }
            }
        }

        $this->line('');
        $this->line('╚════════════════════════════════════════════════════════╝');
    }
}
