<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\Admin\EmployeeCrudController;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => [
        config('backpack.base.web_middleware', 'web'),
        config('backpack.base.middleware_key', 'admin'),
    ],
], function () {
    Route::crud('employees', EmployeeCrudController::class);
});
