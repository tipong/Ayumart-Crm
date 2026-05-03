<?php
/**
 * DEBUG SCRIPT: Fonnte WhatsApp Newsletter Issue Diagnosis
 *
 * Issues Found:
 * 1. Token is invalid (Fonnte API returns "invalid token")
 * 2. Messages show as "sent" in CRM but not delivered to WhatsApp
 * 3. No history in Fonnte dashboard
 *
 * Solution Path:
 * - Get correct device token from Fonnte Dashboard
 * - Update .env with correct FONTE_DEVICE_TOKEN
 * - Test delivery with correct token
 */

require __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

$app = require_once __DIR__ . '/../bootstrap/app.php';

echo "=== FONTE API CONFIGURATION DEBUG ===\n\n";

// Get configuration
$apiKey = config('services.fonnte.api_key');
$deviceToken = config('services.fonnte.device_token');
$baseUrl = config('services.fonnte.base_url');

echo "API Key: " . ($apiKey ? substr($apiKey, 0, 5) . '...' : 'NOT SET') . "\n";
echo "Device Token: " . ($deviceToken ? substr($deviceToken, 0, 5) . '...' : 'NOT SET') . "\n";
echo "Base URL: " . $baseUrl . "\n\n";

if (!$apiKey || !$deviceToken || !$baseUrl) {
    echo "❌ Configuration incomplete!\n";
    exit(1);
}

echo "=== TESTING API CONNECTION ===\n\n";

// Test 1: Check what we're actually sending
echo "1. Checking Authorization Header:\n";
echo "   Using: Authorization: " . $deviceToken . "\n";
echo "   This should be: Authorization: <device_token_from_fonnte>\n\n";

// Test 2: Test with actual device token
echo "2. Testing POST /send with test phone:\n";
$testPhone = "628123456789";
$testMessage = "Test message dari CRM - jika pesan ini masuk, token sudah benar!";

$payload = [
    'target' => $testPhone,
    'message' => $testMessage,
];

echo "   Target: " . $testPhone . "\n";
echo "   Message: " . $testMessage . "\n";
echo "   Sending request...\n\n";

try {
    $response = Http::withHeaders([
        'Authorization' => $deviceToken,
    ])->timeout(30)->post($baseUrl . '/send', $payload);

    $responseData = $response->json();
    $statusCode = $response->status();

    echo "   HTTP Status: " . $statusCode . "\n";
    echo "   Response:\n";
    echo "   " . json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

    if (isset($responseData['status']) && $responseData['status'] === true) {
        echo "✅ SUCCESS! Token is valid and message sent!\n";
        echo "   - Check Fonnte Dashboard for message history\n";
        echo "   - Check target WhatsApp for incoming message\n";
    } else {
        echo "❌ FAILED! Token appears to be invalid or phone format wrong\n";
        if (isset($responseData['reason'])) {
            echo "   Reason: " . $responseData['reason'] . "\n";
        }
        echo "\n   NEXT STEPS:\n";
        echo "   1. Go to https://dashboard.fonnte.com\n";
        echo "   2. Login with your account\n";
        echo "   3. Find your Device Token (NOT API Key)\n";
        echo "   4. Update .env: FONTE_DEVICE_TOKEN=<your_actual_token>\n";
        echo "   5. Run: php artisan config:clear\n";
        echo "   6. Run: php artisan cache:clear\n";
        echo "   7. Try sending newsletter again\n";
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== EXPECTED BEHAVIOR AFTER FIX ===\n\n";
echo "✓ Fonnte API returns: {\"status\": true, \"data\": {...}}\n";
echo "✓ Message appears in Fonnte Dashboard history\n";
echo "✓ Message arrives in target WhatsApp account\n";
echo "✓ CRM shows Newsletter as 'terkirim' (sent)\n";
