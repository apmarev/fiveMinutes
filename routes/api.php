<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmoCrmController;

Route::post('/access/amo/new', [AmoCrmController::class, 'amoNewAccess']);

Route::post('/hooks/amo/dialog', [AmoCrmController::class, 'changeDialog']);
