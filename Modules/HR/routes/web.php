<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\HRController;

Route::resource('hrs', HRController::class)->names('hr');
