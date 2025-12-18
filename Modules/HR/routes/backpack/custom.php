<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\Admin\EmployeeCrudController;
use Modules\HR\Http\Controllers\Admin\EmployeeBankAccountCrudController;
use Modules\HR\Http\Controllers\Admin\EmployeeFileCrudController;
use Modules\HR\Http\Controllers\Admin\EmployeeIdentityCrudController;
use Modules\HR\Http\Controllers\Admin\EmployeeLicenseCrudController;
use Modules\HR\Http\Controllers\Admin\EmployeePhoneCrudController;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => [
        config('backpack.base.web_middleware', 'web'),
        config('backpack.base.middleware_key', 'admin'),
    ],
], function () {
    Route::crud('employees', EmployeeCrudController::class);
    Route::crud('employee-phones', EmployeePhoneCrudController::class);
    Route::crud('employee-identities', EmployeeIdentityCrudController::class);
    Route::crud('employee-licenses', EmployeeLicenseCrudController::class);
    Route::crud('employee-bank-accounts', EmployeeBankAccountCrudController::class);
    Route::crud('employee-files', EmployeeFileCrudController::class);
});
