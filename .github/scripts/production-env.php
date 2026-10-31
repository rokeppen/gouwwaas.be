<?php

/*
 * Writes the production .env from environment variables (GitHub secrets).
 * Usage: php .github/scripts/production-env.php <output-file>
 */

$output = $argv[1] ?? exit("Usage: php production-env.php <output-file>\n");

$required = ['APP_KEY', 'APP_URL', 'DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];

foreach ($required as $name) {
    if (getenv($name) === false || getenv($name) === '') {
        fwrite(STDERR, "Missing required secret: $name\n");
        exit(1);
    }
}

$env = [
    'APP_NAME' => 'Gouw Waas',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'APP_KEY' => getenv('APP_KEY'),
    'APP_URL' => getenv('APP_URL'),
    'APP_LOCALE' => 'nl',
    'APP_FALLBACK_LOCALE' => 'en',

    'LOG_CHANNEL' => 'daily',
    'LOG_LEVEL' => 'warning',

    'DB_CONNECTION' => getenv('DB_CONNECTION') ?: 'mysql',
    'DB_HOST' => getenv('DB_HOST'),
    'DB_PORT' => getenv('DB_PORT') ?: '3306',
    'DB_DATABASE' => getenv('DB_DATABASE'),
    'DB_USERNAME' => getenv('DB_USERNAME'),
    'DB_PASSWORD' => getenv('DB_PASSWORD'),

    'SESSION_DRIVER' => 'database',
    'SESSION_LIFETIME' => '120',
    'SESSION_ENCRYPT' => 'false',
    'SESSION_SECURE_COOKIE' => 'true',
    'CACHE_STORE' => 'database',
    'QUEUE_CONNECTION' => 'database',
    'FILESYSTEM_DISK' => 'local',
];

$lines = [];

foreach ($env as $name => $value) {
    if (preg_match('/[\r\n]/', $value)) {
        fwrite(STDERR, "Secret $name must not contain line breaks\n");
        exit(1);
    }

    // Double quotes with \, " and $ escaped survive phpdotenv parsing unchanged.
    $lines[] = $name . '="' . addcslashes($value, '\\"$') . '"';
}

umask(077);
file_put_contents($output, implode("\n", $lines) . "\n");
