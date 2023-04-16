<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomDetailsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TestController;
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
    Route::get('/user/{id}', 'singleUser');


    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('admin/allUser', 'index');
        Route::post('/changePassword', 'changePassword');
        Route::delete('admin/delete/{id}', 'destroy');
    });
});


Route::controller(RoomDetailsController::class)->group(function () {
    Route::get('/get-room', 'index');
    Route::post('/search', 'search');
    Route::post('/feed', 'feed');

    Route::get('/getroom/{id}', 'show');



    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::put('/room/{id}', 'update');
        Route::post('/shareRoom/{id}', 'shareRoom');
        Route::post('/add-room', 'create');
        Route::post('/store', 'store');
        Route::get('/user-room', 'AddedRoom');
    });
});

Route::controller(BookingController::class)->group(function () {
    Route::get('/allbooking', 'index');
    Route::post('khalti', 'verify');


    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/user-book', 'show');
        Route::post('/book-room/{id}', 'create');

        //not working
        Route::put('/edit-book/{id}', 'edit');
        Route::delete('/delete/{id}', 'destroy');
    });
});

Route::controller(CategoryController::class)->group(function () {
    Route::post('/category', 'create');
    Route::get('/category', 'index');
});

Route::controller(TestController::class)->group(function () {
    Route::post('/map', 'create');
});
