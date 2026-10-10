<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Http\Controllers\Api\MobileApiController;

/*
|--------------------------------------------------------------------------
| Mobile Native API Routes
|--------------------------------------------------------------------------
*/
Route::post('/mobile/login', [MobileApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::get('/mobile/beranda', [MobileApiController::class, 'beranda']);
    Route::get('/mobile/presensi-status', [MobileApiController::class, 'presensiStatus']);
    Route::post('/mobile/checkin', [MobileApiController::class, 'checkIn']);
    Route::post('/mobile/checkout', [MobileApiController::class, 'checkOut']);
    Route::get('/mobile/riwayat', [MobileApiController::class, 'riwayat']);
    Route::get('/mobile/profil', [MobileApiController::class, 'profil']);
    Route::post('/mobile/logout', [MobileApiController::class, 'logout']);
});
