<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('web')->group(function () {
    Route::get('locale/{locale}', [LocaleController::class, 'switch'])
        ->whereIn('locale', config('app.available_locales', []))
        ->name('locale.switch');
});
