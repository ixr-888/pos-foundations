<?php

declare(strict_types=1);

// Read-only checks for the exact project files and namespaces used by this app.
$root = dirname(__DIR__);
$failures = [];
fwrite(STDOUT, "Project: {$root}\n");

if (! is_file($root.'/vendor/autoload.php')) {
    fwrite(STDERR, "Run composer install in the project folder first.\n");
    exit(1);
}
require $root.'/vendor/autoload.php';

foreach ([
    'Database\\Factories\\TaskFactory' => 'database/factories/TaskFactory.php',
    'Database\\Factories\\UserFactory' => 'database/factories/UserFactory.php',
] as $class => $relative) {
    if (! is_file($root.'/'.$relative)) {
        $failures[] = "Missing {$relative}. Extract the complete project archive.";
        continue;
    }
    if (! class_exists($class)) {
        $failures[] = "Cannot autoload {$class}. Run composer dump-autoload --optimize.";
        continue;
    }
    fwrite(STDOUT, "Factory resolved: {$class}\n");
}

$manifest = json_decode(file_get_contents($root.'/composer.json'), true, 512, JSON_THROW_ON_ERROR);
$factoryPaths = (array) ($manifest['autoload']['psr-4']['Database\\Factories\\'] ?? []);
$factoryPaths = array_map(
    static fn (string $path): string => rtrim(str_replace('\\', '/', $path), '/'),
    $factoryPaths
);
if (! in_array('database/factories', $factoryPaths, true)) {
    $failures[] = 'composer.json must map Database\\Factories\\ to database/factories/.';
}

$userCreators = [];
foreach (glob($root.'/database/migrations/*.php') ?: [] as $file) {
    $source = file_get_contents($file);
    if (preg_match('/Schema::create\s*\(\s*[\'"]users[\'"]/', $source) === 1) {
        $userCreators[] = basename($file);
    }
}
fwrite(STDOUT, 'Users-table creation migrations: '.implode(', ', $userCreators)."\n");
if (count($userCreators) !== 1) {
    $failures[] = 'Expected one users-table creation migration. Use a fresh extraction or inspect the listed migrations before continuing.';
} else {
    $source = file_get_contents($root.'/database/migrations/'.$userCreators[0]);
    $canonical = preg_match('/->(?:string|text)\s*\(\s*[\'"]name[\'"]/', $source) === 1;
    $legacy = preg_match('/->(?:string|text)\s*\(\s*[\'"](?:full_name|fullname)[\'"]/', $source) === 1;
    $repair = is_file($root.'/database/migrations/2026_11_16_180000_align_account_name_columns.php');
    if (! $canonical && ! ($legacy && $repair)) {
        $failures[] = 'The users schema must provide name, or a recognized legacy name plus the alignment migration.';
    }
    if (! $canonical && $legacy && $repair) {
        fwrite(STDOUT, "Legacy users name detected; the alignment migration provides the current name column.\n");
    }
}

$dumps = glob($root.'/database/schema/*.sql') ?: [];
if ($dumps !== []) {
    fwrite(STDOUT, "Schema dumps found: ".implode(', ', array_map('basename', $dumps))."\n");
    fwrite(STDOUT, "If the in-memory test schema is wrong, compare these dumps with the current migrations.\n");
}

if ($failures !== []) {
    foreach ($failures as $failure) {
        fwrite(STDERR, "ERROR: {$failure}\n");
    }
    exit(1);
}

fwrite(STDOUT, "Project source/autoload checks passed. Run composer check for the database and application tests.\n");
