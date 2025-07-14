<?php
file_put_contents(__DIR__ . '/../public_debug.txt', 'Public index loaded: ' . __FILE__);

// Увеличиваем лимиты для загрузки файлов (должно быть в самом начале)
ini_set('upload_max_filesize', '20M');
ini_set('post_max_size', '25M');
ini_set('max_execution_time', '300');
ini_set('memory_limit', '256M');
ini_set('max_file_uploads', '50');

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
