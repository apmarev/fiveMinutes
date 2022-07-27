<?php

namespace App\Http\Middleware;

use App\Exceptions\CustomApiException;
use App\Http\Controllers\ReportUsersController;
use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportUserMiddleware {
    public function handle(Request $request, Closure $next) {
        if(!$request->header('User-Token'))
            return CustomApiException::error(401, 'Не передан токен пользователя');

        if($user = ReportUsersController::getByToken($request->header('User-Token'))) {
            $request->attributes->add([
                'user' => $user
            ]);
        } else {
            return CustomApiException::error(401, 'Пользователь с таким токеном не найден');
        }

        return $next($request);
    }
}
