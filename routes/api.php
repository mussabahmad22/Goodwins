<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login',  [ApiController::class, 'login']);

//================== Add Users Api ====================================
Route::post('/add_users' , [ApiController::class, 'add_users']);

//=========================Edit Profile=====================================
Route::post('/edit_user' , [ApiController::class, 'edit_user']);

//========================details Api ===================================
Route::get('/details', [ApiController::class, 'details'])->name('details');

//=========================Subscription Api =======================================
Route::post('/subscription', [ApiController::class, 'subscription'])->name('subscription');

//=========================monthly winners List =======================================
Route::get('/winners', [ApiController::class, 'winners'])->name('winners');

//=========================Subscription Againt User Api =======================================
Route::get('/subscription_against_user', [ApiController::class, 'subscription_against_user'])->name('subscription_against_user');

//=========================contact us Api =====================================
Route::post('/contact', [ApiController::class, 'contact'])->name('contact');

//=========================Forgot Password  Api =====================================
Route::post('/forgot_password', [ApiController::class, 'forgot_password'])->name('forgot_password');

//===================================update password=====================================
Route::post('/pass_update', [ApiController::class, 'pass_update'])->name('pass_update');


