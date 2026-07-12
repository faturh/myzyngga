<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$request = new \Illuminate\Http\Request();
$request->merge([
    'name' => 'Load Test',
    'username' => 'loadtest_' . time(),
    'email' => 'loadtest_' . time() . '@example.com',
    'phone' => '0812345678',
    'password' => 'password123',
    'password_confirmation' => 'password123'
]);

$controller = app(\App\Modules\Auth\Presentation\Http\Controllers\ApiAuthController::class);
try {
    $response = $controller->register($request);
    echo $response->getContent();
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}
