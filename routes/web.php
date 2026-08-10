<?php

use App\Http\Controllers\MercureDemoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MercureDemoController::class, 'index'])->name('login');
Route::get('/login-as/{user}', [MercureDemoController::class, 'loginAs']);
Route::post('/logout', [MercureDemoController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::post('/demo/tick', [MercureDemoController::class, 'tick']);
    Route::post('/demo/message', [MercureDemoController::class, 'sendPrivateMessage']);
});
