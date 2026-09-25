<?php
$routes = json_decode(file_get_contents(__DIR__ . '/routes_dump.json'), true);

$categories = [];

foreach ($routes as $i => $r) {
    $uri = $r['uri'];
    $name = $r['name'];
    $methods = implode('|', $r['methods']);
    $action = $r['action'];
    $middleware = implode(', ', $r['middleware']);

    // determine category
    $cat = 'Other';
    if (str_starts_with($uri, 'login') || $uri === '/' || $uri === 'logout') {
        $cat = 'Guest & Authentication';
    } elseif ($uri === 'dashboard') {
        $cat = 'Dashboard';
    } elseif (str_starts_with($uri, 'customer-tracking')) {
        $cat = 'Customer 360 Tracking';
    } elseif (str_starts_with($uri, 'customer')) {
        $cat = 'Customer Directory';
    } elseif (str_starts_with($uri, 'role')) {
        $cat = 'Roles & Permissions';
    } elseif (str_starts_with($uri, 'user')) {
        $cat = 'User Management';
    } elseif (str_starts_with($uri, 'branch')) {
        $cat = 'Branch / Department';
    } elseif (str_starts_with($uri, 'category')) {
        $cat = 'Category Master';
    } elseif (str_starts_with($uri, 'unit')) {
        $cat = 'Unit Master';
    } elseif (str_starts_with($uri, 'productstock')) {
        $cat = 'Product Stock';
    } elseif (str_starts_with($uri, 'product')) {
        $cat = 'Product Master';
    } elseif (str_starts_with($uri, 'step') || str_starts_with($uri, 'application') || $uri === 'viewapplication') {
        $cat = 'Lease Applications';
    } elseif (str_starts_with($uri, 'mining') || $uri === 'newapplication' || $uri === 'projectfolder' || $uri === 'document' || $uri === 'process') {
        $cat = 'Mining Plan';
    } elseif (str_starts_with($uri, 'eviron') || str_starts_with($uri, 'environ') || str_starts_with($uri, 'environment-b2')) {
        $cat = 'Environment Clearance (B1 / B2)';
    } elseif (str_starts_with($uri, 'ec-certificate')) {
        $cat = 'EC Certificate Issuance';
    } elseif (str_starts_with($uri, 'ppt-department')) {
        $cat = 'PPT Department';
    } elseif (str_starts_with($uri, 'dgps-survey')) {
        $cat = 'DGPS Survey';
    } elseif (str_starts_with($uri, 'drone-survey')) {
        $cat = 'Drone Survey';
    } elseif (str_starts_with($uri, 'ec-compliance')) {
        $cat = 'EC Compliance';
    } elseif (str_starts_with($uri, 'storage') || $uri === 'up') {
        $cat = 'Framework / Storage / Health';
    }

    $categories[$cat][] = [
        'index' => $i + 1,
        'methods' => $methods,
        'uri' => $uri,
        'name' => $name,
        'action' => $action,
        'middleware' => $middleware,
    ];
}

$summary = [];
$total = 0;
foreach ($categories as $cat => $items) {
    $count = count($items);
    $total += $count;
    $summary[$cat] = $count;
}

echo "Total categorized: $total\n";
print_r($summary);

file_put_contents(__DIR__ . '/routes_by_category.json', json_encode($categories, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
