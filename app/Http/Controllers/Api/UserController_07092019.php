<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Validator;
use App\User;
use Hash;
use App\Setting;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
          $this->user = Auth::user();
          return $next($request);
        });
    }

    public function update(Request $request, $id)
    {
        $user = $this->getUserById($id);

        if($user){
          $validate = Validator::make($request->all(), [
            'name' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            //'phone_number' => 'required',
            'height' => 'required',
            //'height_unit' => 'required',
            'weight' => 'required',
            //'weight_unit' => 'required',
            //'exercise_level' => 'required'
          ]);

          if($validate->fails()){
            return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors(),'name' => $request->name), 422);
          }

          $user->name = $request->name;
          $user->date_of_birth = date('Y-m-d',strtotime($request->date_of_birth));
          $user->height = $request->height;
          //$user->height_unit = $request->height_unit;
          $user->weight = $request->weight;
          //$user->weight_unit = $request->weight_unit;
          //$user->exercise_level = $request->exercise_level;
          $user->gender = $request->gender;
          $user->phone_number = $request->phone_number;
          $user->save();

          return response()->json(array('message' => 'Profile has been updated successfully.', 'User' => $user), 200);
        }
        return response()->json(array('message' => 'User not found.'), 404);
    }

    public function updateImage(Request $request, $id)
    {
        $user = $this->getUserById($id);
        if($user){
          $validate = Validator::make($request->all(), [
            'image' => 'required'
          ]);

          if($validate->fails()){
            return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
          }

          if($request->image){
            $image_url = HelperController::imageUpload($request->image, 'user');
            if($image_url){
              $user->image = $image_url;
              $user->save();
            }
          }

          return response()->json(array('message' => 'Image has been updated successfully.', 'image' => $user->image, 'User' => $user), 200);
        }
        return response()->json(array('message' => 'User not found.'), 404);
    }

    public function getUserById($id)
    {
        return User::find($id);
    }

     public function updateFcmToken(Request $request)
    {
        $validate = Validator::make($request->all(), [
          'fcm_token' => 'required'
        ]);

        if($validate->fails()){
          return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
        }

        $user = User::find($this->user->id);
        $user->fcm_token = $request->fcm_token;
        $user->device_type = $request->device;
        $user->save();
        return response()->json(array('message' => 'Fcm token updated successfully.'), 200);
    }

   public function change_password(Request $request){

      if($this->user->id){
          $validate = Validator::make($request->all(), [
          'old_password'     => 'required',
          'new_password'     => 'required|min:6',
        ]);

      if($validate->fails()){
          return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
        }


        $user = User::find($this->user->id);

        if(!Hash::check($request->old_password, $user->password)){

        return response()->json(array('message' => 'The specified password does not match the database password.','status' => 'error'), 422);
        }else{
        $user->update([
        'password' => Hash::make($request->new_password)
        ]); 
        return response()->json(array('message' => 'Your password has been changed successfully.','status' => 'success'), 200);
        } 
      }else{
          return response()->json(array('message' => 'User not found.','status' => 'error'), 404);
      }
    }

  public function getSetting(Request $request)
  {   
    $setting = Setting::find(1); 
    $user = User::find($this->user->id);
    $user->user_app_version = $request->app_version;
    $user->save();
    $setting->reward_text = 'Watch rewarded ads now and earn '.$setting->reward_ads_coin.' Sweatcoins.'; 
    $setting->referal_text = 'Refer a friend & Earn '.$setting->frd_refferal_coin.' Sweatcoins when your friend Sign Up.';
    return $setting;

  }
}
