<?php

$files = scandir(__DIR__ . '/../../../database/migrations');
$files = array_values(array_filter($files, fn($f) => str_ends_with($f, '.php')));
$doc = file_get_contents(__DIR__ . '/../../../docs/03-database.md');
preg_match_all('/`(\d{4}_[a-zA-Z0-9_]+\.php)`/', $doc, $matches);
$docFiles = array_values(array_unique($matches[1]));

echo "Total migrations on disk: " . count($files) . PHP_EOL;
echo "Total migrations in doc: " . count($docFiles) . PHP_EOL;

$missingInDoc = array_diff($files, $docFiles);
$missingOnDisk = array_diff($docFiles, $files);

echo "Missing in doc: " . (empty($missingInDoc) ? "NONE (All 48 present)" : implode(', ', $missingInDoc)) . PHP_EOL;
echo "Missing on disk: " . (empty($missingOnDisk) ? "NONE" : implode(', ', $missingOnDisk)) . PHP_EOL;
