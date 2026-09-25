<?php
require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = app('router')->getRoutes()->getRoutes();
echo "Total routes: " . count($routes) . "\n";

$data = [];
foreach ($routes as $r) {
    $data[] = [
        'methods' => $r->methods(),
        'uri' => $r->uri(),
        'name' => $r->getName(),
        'action' => $r->getActionName(),
        'middleware' => $r->gatherMiddleware(),
    ];
}

file_put_contents(__DIR__ . '/routes_dump.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Saved routes to routes_dump.json\n";
