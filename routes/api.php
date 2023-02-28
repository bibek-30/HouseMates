<?php

use App\Http\Controllers\BookingController;
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


    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/user/{id}', 'singleUser');
        Route::get('admin/allUser', 'index');
        Route::post('/changePassword', 'changePassword');
        Route::delete('admin/delete/{id}', 'destroy');
    });
});


Route::controller(RoomDetailsController::class)->group(function () {
    Route::get('/get-room', 'index');
    Route::post('/add-room', 'create');
    Route::get('/search', 'search');
    Route::get('/getroom/{id}', 'show');


    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::put('/room/{id}', 'update');
        Route::post('/store', 'store');
    });
});

Route::controller(BookingController::class)->group(function () {
    Route::get('/allbooking', 'index');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/booking/{id}', 'create');
        Route::get('/user-book', 'show');
        //not working
        Route::put('/edit-book/{id}', 'edit');
        Route::delete('/delete/{id}', 'destroy');
    });
});
