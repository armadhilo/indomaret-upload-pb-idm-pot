<?php

use App\Http\Controllers\CashierController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KonversiPluController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UploadPotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//LOGIN
Route::post('/login', [LoginController::class, 'login']);
Route::get('/login', [LoginController::class, 'index']);
Route::get('/logout', [LoginController::class, 'logout']);

Route::middleware(['mylogin'])->group(function () {
    //HOME
    Route::group(['prefix' => 'home'], function(){

        Route::get('/', [KonversiPluController::class, 'index']);
        Route::get('/datatables', [KonversiPluController::class, 'datatables']);
        Route::get('/igr-datatables', [KonversiPluController::class, 'helpIgr']);

        Route::group(['prefix' => 'action'], function(){
            Route::post('/save', [KonversiPluController::class, 'actionSave']);
        });
    });

    Route::group(['prefix' => 'upload-pot'], function(){
        Route::get('/', [UploadPotController::class, 'index']);
        Route::post('/check-login', [UploadPotController::class, 'actionLogin']);
        Route::get('/datatables-head', [UploadPotController::class, 'datatablesHead']);
        Route::get('/datatables-detail/{toko}', [UploadPotController::class, 'datatablesDetail']);
        Route::post('/readDbf', [UploadPotController::class, 'readDbf']);
        Route::get('/prosesPBIDM', [UploadPotController::class, 'uploadPot']);
        Route::get('/download-excel/{zipName}', [UploadPotController::class, 'download_excel']);

    });

});
