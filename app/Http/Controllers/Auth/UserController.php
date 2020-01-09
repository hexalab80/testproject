<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\User;
use Validator;


class UserController extends Controller
{
    public function register(Request $request){
      $validate = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|unique:users",
            "password" => "required"
          ]);

      Log::info('auth registration', $request->all());

      if($validate->fails()){
        return response($validate->errors(), 422);
      }

      $user = new User();
      $user->name = $request->name;
      //$user->last_name = $request->last_name;
      $user->email = $request->email;
      $user->password = bcrypt($request->password);
      $user->mobile = $request->mobile;
      $user->role = 1;
      //$user->dob = $request->dob;
     // $user->address = $request->address;
      $user->remember_token = str_random(60);
      $user->save();
      if($user){
        return response('', 201);
      }
    }
}
