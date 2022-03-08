<?php


use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\ApiController;

use App\Http\Controllers\Api\V1\VideoController;
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

/**
 * API User Authentication
 */
Route::post('/login', [LoginController::class, "login"]);
Route::post('/register', [RegisterController::class, "register"]);

/**
 * Authentication Middleware For All Request Except Login
 */
Route::middleware(["auth:sanctum"])->group(function () {
    Route::any('/logout', [LoginController::class, "logout"]);
    Route::get('/get-status-list',[ApiController::class, "getStatusList"]);
    Route::get('/get-pickedUp-status-list',[ApiController::class, "getPickedUpStatusList"]);
    Route::get('/get-parcel',[ApiController::class, "waitingForPickup"]);
    Route::post('/get-parcels-by-status',[ApiController::class, "getParcelByStatus"]);
    Route::get('/get-cancel-hold-reason',[ApiController::class, "getCancelHoldReason"]);
    Route::get('/get-parcels-count-by-status',[ApiController::class, "getParcelCountByStatus"]);
    Route::get('/get-all-parcels',[ApiController::class, "getAllParcels"]);
    Route::post('/change-parcel-status',[ApiController::class, "changeParcelStatus"]);
    Route::get('/get-parcel-history',[ApiController::class, "getParcelHistory"]);
    
   

    
    // need to prepare the json to test
    Route::get('/driver-photo',[ApiController::class, "driverPhoto"]);
    Route::post('/parcel/store',[ApiController::class, "createParcel"]);
    Route::post('/parcel/exchange', [ApiController::class, "exchangeStore"]);

    Route::get('/get-pickedUp-parcels',[ApiController::class, "getPickedUpParcel"]);

    Route::get('/get-delivered-parcels',[ApiController::class, "getDeliveredParcel"]);

    Route::post('/get-parcel-last-history',[ApiController::class, "getParcelLastHistory"]);

    Route::post('/search-parcel',[ApiController::class, "searchParcel"]);

    Route::prefix('video')->group(function(){
        Route::post('/update/{video}', [VideoController::class, 'update']);
    });
});
