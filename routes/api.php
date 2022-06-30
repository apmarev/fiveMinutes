<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmoCrmController;

Route::post('/access/amo/new', [AmoCrmController::class, 'amoNewAccess']);

Route::post('/hooks/amo/dialog/incoming', [AmoCrmController::class, 'incoming']);
Route::post('/hooks/amo/dialog/update', [AmoCrmController::class, 'updateChat']);

Route::get('/amo/users', [AmoCrmController::class, 'getAndSetUsers']);
Route::get('/amo/users/clear', [AmoCrmController::class, 'clearUserTable']);

Route::get('/amo/leads/clear', [AmoCrmController::class, 'clearLeadsTables']);
Route::get('/amo/leads', [AmoCrmController::class, 'getAndSetLeads']);

Route::get('/amo/start', [AmoCrmController::class, 'start']);

Route::get('/report/get', [AmoCrmController::class, 'getToDesktop']);
Route::post('/report/weekend', [AmoCrmController::class, 'setOrRemoveWeekend']);
Route::post('/report/plan', [AmoCrmController::class, 'setPlan']);

Route::post('/hooks/amo/dialog/new', [AmoCrmController::class, 'newTalk']);
Route::post('/hooks/pact/new', [AmoCrmController::class, 'pactNewMessage']);
Route::post('/hooks/senler', [AmoCrmController::class, 'senler']);

Route::post('/unisender', [\App\Http\Controllers\UnisenderController::class, 'getSheet']);

Route::post('/test', [AmoCrmController::class, 'getSenlerQueues']);
