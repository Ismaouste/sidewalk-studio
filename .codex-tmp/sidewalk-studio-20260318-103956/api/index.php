<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

$root = dirname(__DIR__);
$tmpRoot = sys_get_temp_dir().'/sidewalk-studio';
$tmpStorage = $tmpRoot.'/storage';
$tmpBootstrapCache = $tmpRoot.'/bootstrap-cache';
$tmpViews = $tmpStorage.'/framework/views';
$tmpDatabase = $tmpRoot.'/database.sqlite';
$bundledDatabase = $root.'/database/database.sqlite';

$ensureDirectory = static function (string $path): void {
    if (! is_dir($path)) {
        mkdir($path, 0777, true);
    }
};

$setEnv = static function (string $key, string $value): void {
    putenv(sprintf('%s=%s', $key, $value));
    $_ENV[$key] = $value;
    $_SERVER[$key] = $value;
};

$ensureDirectory($tmpRoot);
$ensureDirectory($tmpStorage);
$ensureDirectory($tmpStorage.'/framework/cache/data');
$ensureDirectory($tmpStorage.'/framework/sessions');
$ensureDirectory($tmpStorage.'/framework/testing');
$ensureDirectory($tmpStorage.'/logs');
$ensureDirectory($tmpViews);
$ensureDirectory($tmpBootstrapCache);

if (is_file($bundledDatabase) && ! is_file($tmpDatabase)) {
    copy($bundledDatabase, $tmpDatabase);
}

if (! is_file($tmpDatabase)) {
    touch($tmpDatabase);
}

$scheme = $_SERVER['HTTP_X_FORWARDED_PROTO']
    ?? ((! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http');
$host = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'localhost';

$setEnv('APP_ENV', 'production');
$setEnv('APP_DEBUG', 'false');
$setEnv('APP_KEY', 'base64:6QvG1jYhR3q8q0hD7gC66vS6u7nY9CjK5Aq6tYh0k4c=');
$setEnv('APP_URL', sprintf('%s://%s', $scheme, $host));
$setEnv('SITE_SETTINGS_SOURCE', 'files');
$setEnv('INERTIA_SSR_ENABLED', 'false');
$setEnv('CACHE_STORE', 'array');
$setEnv('SESSION_DRIVER', 'cookie');
$setEnv('LOG_CHANNEL', 'stderr');
$setEnv('LOG_LEVEL', 'warning');
$setEnv('QUEUE_CONNECTION', 'sync');
$setEnv('MAIL_MAILER', 'log');
$setEnv('DB_CONNECTION', 'sqlite');
$setEnv('DB_DATABASE', $tmpDatabase);
$setEnv('LARAVEL_STORAGE_PATH', $tmpStorage);
$setEnv('APP_SERVICES_CACHE', $tmpBootstrapCache.'/services.php');
$setEnv('APP_PACKAGES_CACHE', $tmpBootstrapCache.'/packages.php');
$setEnv('APP_CONFIG_CACHE', $tmpBootstrapCache.'/config.php');
$setEnv('APP_ROUTES_CACHE', $tmpBootstrapCache.'/routes-v7.php');
$setEnv('APP_EVENTS_CACHE', $tmpBootstrapCache.'/events.php');
$setEnv('VIEW_COMPILED_PATH', $tmpViews);

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
