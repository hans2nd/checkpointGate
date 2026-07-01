<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/api/report', 'GET', ['api_key' => 'cpgiic_2026_P0w3rB1_s3cur3K3y!xZ9mQ']);
$response = $kernel->handle($request);

echo "Status: " . $response->getStatusCode() . "\n";
ob_start();
$response->sendContent();
$content = ob_get_clean();
echo "Content: " . substr($content, 0, 500) . "\n";
