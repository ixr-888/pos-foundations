<?php

declare(strict_types=1);

// Portable local checks; run after composer install. Also used by CI.
$root = dirname(__DIR__);
chdir($root);
if (! is_file($root.'/vendor/autoload.php')) {
    fwrite(STDERR, "Run composer install first.\n");
    exit(1);
}

function runCheck(array $arguments): void
{
    $command = implode(' ', array_map('escapeshellarg', $arguments));
    passthru($command, $status);
    if ($status !== 0) {
        exit($status);
    }
}

runCheck([PHP_BINARY, 'artisan', 'config:clear']);
runCheck([PHP_BINARY, 'artisan', 'route:list', '--except-vendor']);
runCheck([PHP_BINARY, 'artisan', 'view:cache']);

$count = 0;
foreach (['app', 'bootstrap', 'config', 'database', 'routes', 'scripts', 'tests', 'storage/framework/views'] as $directory) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root.'/'.$directory, FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            runCheck([PHP_BINARY, '-l', $file->getPathname()]);
            $count++;
        }
    }
}
fwrite(STDOUT, "\nRoutes, compiled Blade views and {$count} PHP files checked.\n");
