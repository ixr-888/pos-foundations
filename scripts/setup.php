<?php

// Run after "composer install". Existing environment settings are preserved.
declare(strict_types=1);

$root = dirname(__DIR__);
chdir($root);

if (PHP_VERSION_ID < 80200) {
    fwrite(STDERR, "Laravel 12 requires PHP 8.2 or newer.\n");
    exit(1);
}

if (! is_file($root.'/vendor/autoload.php')) {
    fwrite(STDERR, "Run composer install before running this setup script.\n");
    exit(1);
}

foreach (['ctype', 'curl', 'dom', 'fileinfo', 'filter', 'hash', 'mbstring', 'openssl', 'pcre', 'pdo', 'pdo_sqlite', 'session', 'tokenizer', 'xml'] as $extension) {
    if (! extension_loaded($extension)) {
        fwrite(STDERR, "Enable the PHP extension: ".$extension."\n");
        exit(1);
    }
}

foreach ([
    'bootstrap/cache',
    'storage/app/private',
    'storage/app/public',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
] as $directory) {
    if (! is_dir($root.'/'.$directory)) {
        mkdir($root.'/'.$directory, 0775, true);
    }
}

if (! is_file($root.'/.env')) {
    copy($root.'/.env.example', $root.'/.env');
}

$environment = file_get_contents($root.'/.env');

if (preg_match('/^APP_ENV\s*=\s*["\']?production["\']?\s*$/m', $environment)) {
    fwrite(STDERR, "For production, follow the deployment instructions in README.md.\n");
    exit(1);
}

$sqlite = $root.'/database/database.sqlite';
if (! is_file($sqlite)) {
    touch($sqlite);
}

function artisan(string $arguments): void
{
    passthru(escapeshellarg(PHP_BINARY).' '.escapeshellarg(__DIR__.'/../artisan').' '.$arguments, $status);
    if ($status !== 0) {
        exit($status);
    }
}

// Clear any stale configuration before generating a key or migrating.
artisan('config:clear');

preg_match('/^APP_KEY[ \t]*=[ \t]*(.*)$/m', $environment, $keyMatch);
$existingKey = trim($keyMatch[1] ?? '', " \t\r\n\"'");

if ($existingKey === '') {
    artisan('key:generate --ansi');
}

artisan('migrate --seed --ansi');
artisan('route:list --except-vendor');

fwrite(STDOUT, "\nSetup complete. Run: php artisan serve\n");
fwrite(STDOUT, "Then open: http://localhost:8000\n");
