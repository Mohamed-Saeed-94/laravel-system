<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Http\Controllers\Admin\BranchCrudController;
use Modules\Core\Http\Controllers\Admin\CityCrudController;
use Modules\Core\Http\Controllers\Admin\DepartmentCrudController;
use Modules\Core\Http\Controllers\Admin\JobTitleCrudController;

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => [
        config('backpack.base.web_middleware', 'web'),
        config('backpack.base.middleware_key', 'admin'),
    ],
], function () {
    Route::crud('cities', CityCrudController::class);
    Route::crud('branches', BranchCrudController::class);
    Route::crud('departments', DepartmentCrudController::class);
    Route::crud('job-titles', JobTitleCrudController::class);
});
