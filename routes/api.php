<?php

use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\FriendController;
use App\Http\Controllers\Api\SessionController;
use App\Http\Controllers\Api\UserController;
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

Route::post('/customer_register', [CustomerController::class, 'store'])->name('customer_register.user');
Route::put('/create_user', [UserController ::class, 'create_user'])->name('create_user.user');
Route::post('/login', [SessionController::class, 'login'])->name('login.api');

Route::middleware('auth:api')->group(function() {
    Route::get('/logout', [SessionController::class, 'logout'])->name('logout.api');
    Route::get('/profile', [SessionController::class, 'profile'])->name('profile.api');
    
    
    Route::group(['middleware' => ['UserTypeCheck:Customer']], function () {
        Route::post('/customer_search', [CustomerController::class, 'search'])->name('customer.search');
        Route::post('/friend_request', [FriendController::class, 'send_request'])->name('friend.store');
        Route::get('/see_request', [FriendController::class, 'see_request'])->name('friend.see_request');
        Route::get('/user_request/{id}/accept', [FriendController::class, 'approve'])->name('friend.approve');
        Route::get('/see_mutual_friend/{id}', [FriendController::class, 'see_mutual_friend']);
    });
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
