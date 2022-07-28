<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AmoCrmController;
use App\Http\Controllers\ReportUsersController;

Route::post('/v1/report/user/login', [ReportUsersController::class, 'login']);

Route::prefix('v1')->group(function() {
    Route::prefix('report')->group(function() {
        Route::get('/info', [AmoCrmController::class, 'getWebInfoManager']);
        Route::get('/main', [AmoCrmController::class, 'getWebInfoMain']);
        Route::get('/managers', [AmoCrmController::class, 'getWebManagers']);
        Route::get('/years', [AmoCrmController::class, 'getWebYears']);
        Route::get('/month/{year}', [AmoCrmController::class, 'getWebMonthByYear']);
        Route::get('/plan', [AmoCrmController::class, 'getManagersPlan']);
        Route::get('/filter_plan', [AmoCrmController::class, 'getFilterPlan']);

        Route::post('/plan', [AmoCrmController::class, 'setManagersPlan']);

        Route::prefix('user')->group(function() {
            Route::get('/', [ReportUsersController::class, 'get']);
            Route::post('/', [ReportUsersController::class, 'create']);
            Route::put('/{id}', [ReportUsersController::class, 'edit']);
            Route::delete('/{id}', [ReportUsersController::class, 'delete']);
        });
    });
});

Route::post('/access/amo/new', [AmoCrmController::class, 'amoNewAccess']);

Route::post('/generate', [AmoCrmController::class, 'generate']);

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

Route::post('/test', [AmoCrmController::class, 'getManagersInfo']);

Route::post('/csv', [AmoCrmController::class, 'getTestCSV']);
