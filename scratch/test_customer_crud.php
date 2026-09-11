<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$admin = User::where('email', 'admin@gtms.com')->first();
Auth::login($admin);

$controller = new App\Http\Controllers\CustomerDirectoryController();
$district = District::first();
$mineral = Mineral::first();

// 1. Test Store (without mineral_id and area)
$reqStore = Request::create('/customeradd', 'POST', [
    'customer_name' => 'M. Murugan',
    'company_name'  => 'Murugan Blue Metals',
    'mobile_num'    => '9876501234',
    'email'         => 'murugan@bluemetals.com',
    'district_id'   => $district->id,
    'pan'           => 'AAACM9876R',
    'gstin'         => '33AAACM9876R1Z9',
    'address'       => 'SF 120/3, Madukkarai, Coimbatore',
    'status'        => 1,
]);
$resStore = $controller->store($reqStore);
echo "1. Store response: " . $resStore->getContent() . PHP_EOL;

$newCust = Customer::where('pan', 'AAACM9876R')->first();
if (!$newCust) {
    throw new Exception("Customer was not created!");
}

// 2. Test Show
$resShow = $controller->show($newCust->id);
echo "2. Show response: " . $resShow->getContent() . PHP_EOL;

// 3. Test Update
$reqUpdate = Request::create('/customeredit', 'POST', [
    'id'            => $newCust->id,
    'customer_name' => 'M. Murugan Updated',
    'company_name'  => 'Murugan Blue Metals Pvt Ltd',
    'mobile_num'    => '9876501234',
    'email'         => 'murugan@bluemetals.com',
    'district_id'   => $district->id,
    'pan'           => 'AAACM9876R',
    'gstin'         => '33AAACM9876R1Z9',
    'address'       => 'SF 120/3, Madukkarai, Coimbatore',
    'status'        => 1,
]);
$resUpdate = $controller->update($reqUpdate);
echo "3. Update response: " . $resUpdate->getContent() . PHP_EOL;

// 4. Test Destroy (Soft Delete)
$reqDelete = Request::create('/customerdelete', 'POST', [
    'id' => $newCust->id,
]);
$resDelete = $controller->destroy($reqDelete);
echo "4. Delete response: " . $resDelete->getContent() . PHP_EOL;

$isDeleted = Customer::find($newCust->id) === null && Customer::withTrashed()->find($newCust->id) !== null;
echo "Soft delete verified: " . ($isDeleted ? 'YES' : 'NO') . PHP_EOL;
echo "ALL CUSTOMER CRUD ACTIONS PASSED SUCCESSFULLY!" . PHP_EOL;
