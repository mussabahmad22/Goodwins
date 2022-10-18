<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Models\User;
use App\Models\MonthlyWinner;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    $users = User::all()->count();
    $winners = MonthlyWinner::all()->count();
    return view('dashboard', compact('users','winners'));
})->middleware('auth');

Route::middleware('auth')->group(function () {

    Route::get('/admin_logout', [AdminController::class, 'logout'])->name('admin_logout');
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    //=========================== users ===========================================
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/delete_user' , [AdminController::class, 'delete_user'])->name('delete_user');
   
    //=========================== details ===========================================
    Route::get('/details', [AdminController::class, 'details'])->name('details');
    Route::post('/add_detail', [AdminController::class, 'add_detail'])->name('add_detail');
    Route::get('edit_detail/{id}', [AdminController::class, 'edit_detail'])->name('edit_detail');
    Route::PUT('detail_update', [AdminController::class, 'detail_update'])->name('detail_update');
    Route::delete('/delete_detail' , [AdminController::class, 'delete_detail'])->name('delete_detail');

    //==========================subscription routes =================================================
    Route::get('/subscription', [AdminController::class, 'subscription'])->name('subscription');
    Route::get('/subscription_by_month/{id}', [AdminController::class, 'subscription'])->name('subscription_by_month');

    //==================================lucky draw =============================================
    Route::get('/lucky_draw', [AdminController::class, 'lucky_draw'])->name('lucky_draw');
    Route::delete('/delete_subscription' , [AdminController::class, 'delete_subscription'])->name('delete_subscription');

    //=====================================winner api============================================
    Route::get('/winners', [AdminController::class, 'winners'])->name('winners');
    Route::delete('/delete_winner' , [AdminController::class, 'delete_winner'])->name('delete_winner');

    //========================================contact us api========================================
    Route::get('/contact', [AdminController::class, 'contact'])->name('contact');
    Route::delete('/delete_contact' , [AdminController::class, 'delete_contact'])->name('delete_contact');
  

});

Route::get('/dashboard', function () {
    $users = User::all()->count();
    $winners = MonthlyWinner::all()->count();
    return view('dashboard', compact('users','winners'));
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';
