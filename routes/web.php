<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('2fa');

Route::group(['prefix'=>'2fa'], function(){
    Route::get('/', [App\Http\Controllers\Google2faController::class, 'show2faForm'])->name('show2faForm');
    Route::post('/generateSecret', [App\Http\Controllers\Google2faController::class, 'generate2faSecret'])->name('generate2faSecret');
    Route::post('/enable2fa', [App\Http\Controllers\Google2faController::class, 'enable2fa'])->name('enable2fa');
    Route::post('/disable2fa', [App\Http\Controllers\Google2faController::class, 'disable2fa'])->name('disable2fa');

    // 2fa middleware
    Route::get('/2faVerify', [App\Http\Controllers\Google2faController::class, 'show2faverify'])->name('show2faverify');
    Route::post('/2faVerify', [App\Http\Controllers\Google2faController::class, 'google2faverify'])->name('2faVerify');
});

Route::get('handle-payment', [App\Http\Controllers\PayPalPaymentController::class, 'handlePayment'])->name('make.payment');
Route::post('create-order', [App\Http\Controllers\PayPalPaymentController::class, 'createOrder'])->name('create.order');
Route::get('cancel-payment', [App\Http\Controllers\PayPalPaymentController::class, 'paymentCancel'])->name('cancel.payment');
Route::get('payment-success', [App\Http\Controllers\PayPalPaymentController::class, 'paymentSuccess'])->name('success.payment');