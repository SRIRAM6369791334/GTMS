<?php
require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controllersPath = __DIR__ . '/../../../app/Http/Controllers';
$files = scandir($controllersPath);

$allControllers = [];

foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) !== 'php') continue;
    $className = 'App\\Http\\Controllers\\' . pathinfo($file, PATHINFO_FILENAME);
    if (!class_exists($className)) continue;

    $ref = new ReflectionClass($className);
    $methods = [];
    foreach ($ref->getMethods() as $m) {
        if ($m->class === $className) {
            $params = [];
            foreach ($m->getParameters() as $p) {
                $params[] = ($p->hasType() ? (string)$p->getType() . ' ' : '') . '$' . $p->getName() . ($p->isDefaultValueAvailable() ? ' = ' . json_encode($p->getDefaultValue()) : '');
            }
            $methods[] = [
                'name' => $m->getName(),
                'visibility' => $m->isPublic() ? 'public' : ($m->isProtected() ? 'protected' : 'private'),
                'isStatic' => $m->isStatic(),
                'parameters' => $params,
                'startLine' => $m->getStartLine(),
                'endLine' => $m->getEndLine(),
                'docComment' => $m->getDocComment() ? trim($m->getDocComment()) : null,
            ];
        }
    }

    $allControllers[$file] = [
        'class' => $className,
        'parent' => $ref->getParentClass() ? $ref->getParentClass()->getName() : null,
        'isAbstract' => $ref->isAbstract(),
        'totalLines' => $ref->getEndLine() - $ref->getStartLine() + 1,
        'methods' => $methods,
    ];
}

file_put_contents(__DIR__ . '/controllers_reflection.json', json_encode($allControllers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "Reflected " . count($allControllers) . " controllers into controllers_reflection.json\n";
