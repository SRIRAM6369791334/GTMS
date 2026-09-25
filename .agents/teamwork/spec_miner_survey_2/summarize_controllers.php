<?php
$c = json_decode(file_get_contents(__DIR__ . '/controllers_reflection.json'), true);
foreach ($c as $file => $info) {
    echo $file . " (" . count($info['methods']) . " methods):\n";
    foreach ($info['methods'] as $m) {
        echo "  - " . $m['visibility'] . " " . $m['name'] . "(" . implode(', ', $m['parameters']) . "): lines " . $m['startLine'] . "-" . $m['endLine'] . "\n";
    }
    echo "\n";
}
