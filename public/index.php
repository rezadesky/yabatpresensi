<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Deteksi Lokasi Direktori Utama Laravel
|--------------------------------------------------------------------------
| 1. Jika di localhost: ../
| 2. Jika di cPanel public_html/yabatpresensi.stkip-us.ac.id: ../../yabatpresensi
*/
$laravelPath = __DIR__ . '/..';
if (!file_exists($laravelPath . '/vendor/autoload.php')) {
    if (file_exists(__DIR__ . '/../../yabatpresensi/vendor/autoload.php')) {
        $laravelPath = __DIR__ . '/../../yabatpresensi';
    }
}

if (file_exists($maintenance = $laravelPath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $laravelPath . '/vendor/autoload.php';

$app = require_once $laravelPath . '/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
