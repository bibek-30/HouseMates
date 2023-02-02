<?php

use App\Http\Controllers\RoomDetailsController;
use App\Http\Controllers\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'create');
    Route::post('/login', 'login')->name('login');
});


Route::controller(RoomDetailsController::class)->group(function () {
    Route::post('/add-room', 'create');
    Route::get('/get-room', 'index');
});
