<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportWebController;


Route::get('/', [ReportWebController::class, 'report']);
