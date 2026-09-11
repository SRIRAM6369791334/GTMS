<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

// Authenticate as Admin user
$admin = User::where('email', 'admin@gtms.com')->first();
Auth::login($admin);

echo "Logged in as: " . Auth::user()->name . " (Role: " . Auth::user()->roles->pluck('name')->join(', ') . ")" . PHP_EOL;

$controller = new App\Http\Controllers\CustomerDirectoryController();
$view = $controller->index();
$rendered = $view->render();

echo "Rendered view length: " . strlen($rendered) . " bytes." . PHP_EOL;
echo "Contains 'Customer Directory': " . (str_contains($rendered, 'Customer Directory') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains 'Sri Bala Traders': " . (str_contains($rendered, 'Sri Bala Traders') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains 'Total Customers': " . (str_contains($rendered, 'Total Customers') ? 'YES' : 'NO') . PHP_EOL;
echo "Customer count in DB: " . Customer::count() . PHP_EOL;
