<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$leaseStatuses = \App\Models\LeaseApplication::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))->groupBy('status')->get();
$miningStatuses = \App\Models\MiningApplication::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))->groupBy('status')->get();

echo "Lease Statuses: " . json_encode($leaseStatuses) . "\n";
echo "Mining Statuses: " . json_encode($miningStatuses) . "\n";
