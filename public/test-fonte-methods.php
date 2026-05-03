<?php

require __DIR__ . '/../bootstrap/app.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

try {
    $fonte = $app->make('App\Services\FonnteService');
    echo "✓ FonnteService instantiated\n";

    echo "✓ isConfigured(): " . ($fonte->isConfigured() ? 'YES' : 'NO') . "\n";
    echo "✓ getContactsCount(): " . $fonte->getContactsCount() . "\n";
    echo "✓ Method sendNewsletter exists: " . (method_exists($fonte, 'sendNewsletter') ? 'YES' : 'NO') . "\n";
    echo "✓ Method getContacts exists: " . (method_exists($fonte, 'getContacts') ? 'YES' : 'NO') . "\n";
    echo "✓ Method sendMessage exists: " . (method_exists($fonte, 'sendMessage') ? 'YES' : 'NO') . "\n";

    echo "\n✅ All methods are callable and present!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
