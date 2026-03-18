<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$root = dirname(__DIR__);

$headerValue = static function (string $key): ?string {
    $value = $_SERVER[$key] ?? null;

    if (! is_string($value) || $value === '') {
        return null;
    }

    $firstValue = trim(explode(',', $value)[0]);

    return $firstValue === '' ? null : $firstValue;
};

$envValue = static function (string $key): ?string {
    $value = getenv($key);

    if (is_string($value) && $value !== '') {
        return $value;
    }

    foreach ([$_ENV[$key] ?? null, $_SERVER[$key] ?? null] as $candidate) {
        if (is_string($candidate) && $candidate !== '') {
            return $candidate;
        }
    }

    return null;
};

$setEnv = static function (string $key, string $value): void {
    putenv(sprintf('%s=%s', $key, $value));
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
};

$setEnvDefault = static function (string $key, string $value) use ($envValue, $setEnv): void {
    if ($envValue($key) !== null) {
        return;
    }

    $setEnv($key, $value);
};

$ensureDirectory = static function (string $path): void {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
};

$scheme = strtolower(
    $headerValue('HTTP_X_FORWARDED_PROTO')
    ?? ((! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http')
);
$host = $headerValue('HTTP_X_FORWARDED_HOST')
    ?? $headerValue('HTTP_HOST')
    ?? 'localhost';
$publicUrl = sprintf('%s://%s', $scheme, $host);
$runtimeHash = substr(hash('sha256', $host), 0, 16);
$tmpRoot = sys_get_temp_dir().'/sidewalk-studio-'.$runtimeHash;
$tmpStorage = $tmpRoot.'/storage';
$tmpBootstrapCache = $tmpRoot.'/bootstrap-cache';
$tmpViews = $tmpStorage.'/framework/views';
$tmpDatabase = $tmpRoot.'/database.sqlite';
$bundledDatabase = $root.'/database/database.sqlite';

$ensureDirectory($tmpStorage.'/framework/cache/data');
$ensureDirectory($tmpStorage.'/framework/sessions');
$ensureDirectory($tmpStorage.'/framework/testing');
$ensureDirectory($tmpStorage.'/logs');
$ensureDirectory($tmpViews);
$ensureDirectory($tmpBootstrapCache);

$setEnvDefault('APP_ENV', 'production');
$setEnvDefault('APP_DEBUG', 'false');
$setEnvDefault('APP_KEY', 'base64:'.base64_encode(hash('sha256', $host, true)));
$setEnvDefault('APP_URL', $publicUrl);
$setEnvDefault('SITE_SETTINGS_SOURCE', 'files');
$setEnvDefault('INERTIA_SSR_ENABLED', 'false');
$setEnvDefault('CACHE_STORE', 'array');
$setEnvDefault('SESSION_DRIVER', 'cookie');
$setEnvDefault('LOG_CHANNEL', 'stderr');
$setEnvDefault('LOG_LEVEL', 'warning');
$setEnvDefault('QUEUE_CONNECTION', 'sync');
$setEnvDefault('MAIL_MAILER', 'log');
$setEnvDefault('LARAVEL_STORAGE_PATH', $tmpStorage);
$setEnvDefault('APP_SERVICES_CACHE', $tmpBootstrapCache.'/services.php');
$setEnvDefault('APP_PACKAGES_CACHE', $tmpBootstrapCache.'/packages.php');
$setEnvDefault('APP_CONFIG_CACHE', $tmpBootstrapCache.'/config.php');
$setEnvDefault('APP_ROUTES_CACHE', $tmpBootstrapCache.'/routes-v7.php');
$setEnvDefault('APP_EVENTS_CACHE', $tmpBootstrapCache.'/events.php');
$setEnvDefault('VIEW_COMPILED_PATH', $tmpViews);

$databaseConnection = strtolower($envValue('DB_CONNECTION') ?? '');

if ($databaseConnection === '') {
    $databaseConnection = 'sqlite';
    $setEnv('DB_CONNECTION', $databaseConnection);
}

if ($databaseConnection === 'sqlite' && $envValue('DB_DATABASE') === null) {
    if (is_file($bundledDatabase) && ! is_file($tmpDatabase)) {
        copy($bundledDatabase, $tmpDatabase);
    }

    if (! is_file($tmpDatabase)) {
        touch($tmpDatabase);
    }

    $setEnv('DB_DATABASE', $tmpDatabase);
}

$_SERVER['DOCUMENT_ROOT'] = $root.'/public';
$_SERVER['SCRIPT_FILENAME'] = $root.'/public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

define('LARAVEL_START', microtime(true));

$maintenance = $root.'/storage/framework/maintenance.php';

if (is_file($maintenance)) {
    require $maintenance;
}

require $root.'/vendor/autoload.php';

/** @var Application $app */
$app = require $root.'/bootstrap/app.php';

if (preg_match('/^[A-Za-z]:\\\\/', $tmpBootstrapCache) === 1) {
    $app->addAbsoluteCachePathPrefix(substr($tmpBootstrapCache, 0, 2));
}

$app->handleRequest(Request::capture());
