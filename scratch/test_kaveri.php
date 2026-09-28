<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first()
    ?: App\Models\User::first();
auth()->login($user);

$customer = App\Models\Customer::where('slug', 'kaveri-granites-3197')->first();
if (!$customer) {
    echo "Kaveri not found by slug, finding first customer with leases...\n";
    $customer = App\Models\Customer::has('leaseApplications')->first();
}

echo "Testing customer: " . $customer->customer_name . " (ID: " . $customer->id . ", Slug: " . $customer->slug . ")\n";
echo "District: " . ($customer->district?->name ?? 'NULL') . "\n";
echo "Leases count: " . $customer->leaseApplications()->count() . "\n";
echo "Mining count: " . $customer->miningApplications()->count() . "\n";
echo "Env count: " . $customer->environmentProjects()->count() . "\n";
echo "Compliance count: " . $customer->ecCompliances()->count() . "\n";

$startTime = microtime(true);
$controller = app(App\Http\Controllers\CustomerTrackingController::class);
try {
    $view = $controller->show($customer->slug);
    $renderStartTime = microtime(true);
    $html = $view->render();
    $endTime = microtime(true);
    echo "Controller time: " . round(($renderStartTime - $startTime) * 1000, 2) . "ms\n";
    echo "Render time: " . round(($endTime - $renderStartTime) * 1000, 2) . "ms\n";
    echo "Total time: " . round(($endTime - $startTime) * 1000, 2) . "ms\n";
    echo "HTML size: " . strlen($html) . " bytes\n";
    echo "SUCCESS: Page rendered without error!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
