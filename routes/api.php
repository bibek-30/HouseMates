<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\RoomDetailsController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

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

Route::get('/users/count', function () {
    return response()->json([
        'count' => User::countUsers()
    ]);
});

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'create');

    Route::post('/login', 'login')->name('login');
    Route::get('/user/{id}', 'singleUser');
    Route::get('/user/count', 'countUsers');
    Route::get('admin/allUser', 'index');
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::post('/changePassword', 'changePassword');
        Route::delete('admin/delete/{id}', 'destroy');
    });
});


Route::controller(RoomDetailsController::class)->group(function () {
    Route::get('/get-room', 'index');

    Route::post('/search', 'search');
    Route::post('/feed', 'feed');
    Route::get('/room/count', 'RoomCount');
    Route::get('/getroom/{id}', 'show');
    Route::get('/myshare', 'mySharedRooms');
    Route::get('/shareList', 'ShareList');
    Route::delete('/room/delete/{id}', 'delete');
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::put('/room/edit/{id}', 'update');


        Route::post('/shareRoom/{id}', 'shareRoom');
        Route::post('/add-room', 'create');
        Route::post('/store', 'store');
        Route::get('/user-room', 'AddedRoom');
        Route::put('/removeShare/{id}', 'RemoveSharedRoom');
    });
});

Route::controller(BookingController::class)->group(function () {
    Route::get('/allbooking', 'index');
    Route::get('/book/count', 'Count');
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/user-book', 'show');
        Route::post('/book-room/{id}', 'create');
        //not working
        Route::put('/edit-book/{id}', 'edit');
        Route::delete('/delete/{id}', 'destroy');
    });
});


Route::controller(PaymentController::class)->group(function () {
    Route::post('khalti', 'verify');
    Route::get('showPayment', 'Show');


    Route::group(['middleware' => 'auth:sanctum'], function () {
    });
});
