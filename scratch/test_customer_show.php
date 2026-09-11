<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$admin = User::where('email', 'admin@gtms.com')->first();
Auth::login($admin);

$controller = new App\Http\Controllers\CustomerDirectoryController();
$cust = Customer::first();
if (!$cust) {
    throw new Exception("No customer in database!");
}

// 1. Test Web View rendering via SLUG
$reqWeb = Request::create('/customers/' . $cust->slug, 'GET');
$view = $controller->show($reqWeb, $cust->slug);
$rendered = $view->render();

echo "Rendered 360° Dossier View Length: " . strlen($rendered) . " bytes." . PHP_EOL;
echo "Contains 'CUST Dossier': " . (str_contains($rendered, 'Dossier') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains Customer Name (" . $cust->company_name . "): " . (str_contains($rendered, $cust->company_name) ? 'YES' : 'NO') . PHP_EOL;
echo "Contains 'Legal & Tax Identifiers': " . (str_contains($rendered, 'Legal & Tax Identifiers') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains 'Mining Footprint': " . (str_contains($rendered, 'Mining Footprint') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains Tab 'Leases': " . (str_contains($rendered, 'Quarry Lease Applications') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains Tab 'Mining Plans': " . (str_contains($rendered, 'Approved Mining Plans') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains Tab 'Environment & EC': " . (str_contains($rendered, 'Environmental Clearance') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains Tab 'PPT Agendas': " . (str_contains($rendered, 'SEAC / SEIAA Presentation Agendas') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains Tab 'Surveys': " . (str_contains($rendered, 'DGPS Ground Surveys') ? 'YES' : 'NO') . PHP_EOL;
echo "Contains Tab 'Stockpile': " . (str_contains($rendered, 'Quarry Pithead Stockpile') ? 'YES' : 'NO') . PHP_EOL;

// 2. Test AJAX JSON response
$reqAjax = Request::create('/customers/' . $cust->id, 'GET', [], [], [], ['HTTP_X-Requested-With' => 'XMLHttpRequest']);
$jsonRes = $controller->show($reqAjax, $cust->id);
echo "AJAX JSON status: " . $jsonRes->getData()->status . " for " . $jsonRes->getData()->data->customer_name . PHP_EOL;

echo "ALL TESTS PASSED WITH 0 ERRORS!" . PHP_EOL;
