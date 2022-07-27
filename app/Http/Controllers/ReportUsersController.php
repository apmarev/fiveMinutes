<?php

namespace App\Http\Controllers;

use App\Exceptions\CustomApiException;
use App\Models\ReportUser;
use Illuminate\Http\Request;

class ReportUsersController extends Controller {

   public static function getByToken(string $token): bool | ReportUser {
       if($user = ReportUser::where('token', $token)->first()) return $user; else return false;
   }

   public function get() {
       return ReportUser::all();
   }

   public function create(Request $request) {

       if(!$request->has('login') || !$request->has('password') || !$request->has('super'))
           return CustomApiException::error(400);

       $token = sha1($request->input('login') . $request->input('password') . $request->input('super') . time());

       $user = new ReportUser();
       $user->__set('login', $request->input('login'));
       $user->__set('password', $request->input('password'));
       $user->__set('super', $request->input('super'));
       $user->__set('token', $token);

       $user->save();
       return "Ok";
   }

   public function edit(Request $request, int $id) {
       if(!$request->has('login') || !$request->has('password') || !$request->has('super'))
           return CustomApiException::error(400);

       if($user = ReportUser::find($id)) {
           $token = sha1($request->input('login') . $request->input('password') . $request->input('super') . time());
           $user->__set('login', $request->input('login'));
           $user->__set('password', $request->input('password'));
           $user->__set('super', $request->input('super'));
           $user->__set('token', $token);

           $user->save();
           return "Ok";
       } else {
           return CustomApiException::error(404, 'Пользователь с таким ID не найден');
       }
   }

   public function login(Request $request) {
       if($user = ReportUser::where('login', $request->input('login'))->where('password', $request->input('password'))->first()) {
           return $user;
       } else {
           return CustomApiException::error(404, 'User not found');
       }
   }

   public function delete(int $id) {
       if($user = ReportUser::find($id)) {
           $user->delete();
           return "Ok";
       } else {
           return CustomApiException::error(404, 'User not found');
       }
   }

}
