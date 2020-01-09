<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Validator;
use App\User;
use Hash;
use App\Setting;
use App\RewardCoin;
use App\Step;
use App\Wallet;


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
    $reward_ads_info = RewardCoin::where('user_id',$this->user->id)->where('reward_type','2')->orderBy('id','desc')->first();
    if($reward_ads_info){
      $reward_ads_info->ads_timestamp = strtotime($reward_ads_info->created_at);
    }
    
   // $reward_ads_info->ads_timestamp1 = microtime($reward_ads_info->created_at);
    $user->user_app_version = $request->app_version;

    $setting->reward_text = 'Watch rewarded ads now and earn '.$setting->reward_ads_coin.' Sweatcoins.'; 
    $setting->referal_text = 'Refer a friend & Earn '.$setting->frd_refferal_coin.' Sweatcoins when your friend Sign Up.';
    $setting->lucky_coupon_text = 'Claim your lucky coupon in every one hour and get additional Sweat Coin to earn more.';
   // $setting->reward_active = 0;
    $setting->current_time = date('Y-m-d H:i:s');
    $setting->reward_ads_info = $reward_ads_info;
    $setting->condition = '<ol><li> The amount should be a maximum of 60 percent of the wallet balance.</li><li> It may take up to 24-72 working hours for the amount to get credited in your above mentioned PayTm account.</li><ol>';

   
    $wallet_info = Wallet::join('rewards','rewards.id','=','wallets.reward_id')->selectRaw("SUM(rewards.value) as redeem_coins")->where('user_id',$this->user->id)->first();
    $getRewardCoin = RewardCoin::selectRaw('SUM(coins) as total_coin')->where('user_id',$this->user->id)->first();
    if(!empty($wallet_info) && !empty($getRewardCoin)){
    
     // $user = User::find($this->user->id);
      $totalsteps = Step::selectRaw('SUM(steps) as totalsteps')->where('user_id',$this->user->id)->orderBy('id','desc')->first();
      if($totalsteps->totalsteps > 0){
        $user->total_steps = $totalsteps->totalsteps;  
        $totalcoins =floor($totalsteps->totalsteps/100);
        $user->redeem_coins = $wallet_info->redeem_coins;
        $get_redeem_coin = $getRewardCoin->total_coin + $totalcoins;
        $user->available_coins = $get_redeem_coin - $wallet_info->redeem_coins;
      }  
    }
    $user->save();
    $setting->user = $user;
    //lucky coupons

    if($setting->lucky_pop=='1'){
      $date = date('Y-m-d');
      $check = RewardCoin::where('user_id',$this->user->id)->where('reward_type','3')->whereDate('created_at','=',$date)->orderBy('id','desc')->first();
      $setting->lucky_coupon_info = $check;
      if(empty($check)){
        $setting->flag = '1';
      }else{
        $start_time = strtotime(date('H:i:s',strtotime($check->created_at)));
        $timediff = strtotime(date('H:i:s')) - $start_time;
        $setting->timediff = $timediff;
        
        if($timediff > 3600){
          $setting->flag = '1';
        }else{
          $setting->flag = '0';
        }
      }
      $setting->lucky_coins = $this->generate_slab($setting->lucky_min,$setting->lucky_max);
    }else{
      $setting->flag = '0';
    } 
    return $setting;
  }

  public function generate_slab($first,$last){

    return rand($first,$last);
  }
}
