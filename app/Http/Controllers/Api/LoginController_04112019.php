<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

use Auth;
use Validator;
use Hash;
use Carbon\Carbon;

use App\User;
use App\Social;
use App\Mail\VerifyEmailOtp;
use App\Mail\VerifyEmailLink;
use App\Mail\ValidEmail;
use App\Wallet;
//use App\PaytmTranscation;
use App\PaytmRequest;
use App\Reward;
use App\Setting;
use App\RewardCoin;
//use App\OauthAccessToken;

use App\Http\Controllers\Api\FcmNotificationController as FcmNotificationController;

class LoginController extends Controller
{
    public function __cosntruct()
    {
        $this->middleware(function($request, $next){
          $this->user = Auth::user();
        });
        //$this->now = Carbon::now();
    }

    public function signUp(Request $request)
    {
        $validate = Validator::make($request->all(), [
          'email' => 'required|email|unique:users,email',
          'password' => 'required|min:6',
          'name' => 'required|alpha|max:25'
        ]);

        if($validate->fails()){
          return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
        }

        $denied_hostnames = array("bcaoo.com","ixaes.com","net1mail.com","urhen.com","ixaks.com","ymail.com","oqiwq.com");
        foreach ($denied_hostnames as $hn)
        {
          if (strstr($request->email, "@" . $hn))
          {
            return response()->json(array('message' => "Sorry! We don't accept " . $hn . " email addresses.", 'status' => 'error'), 422);
          }
        }


        if($request->frd_code !='' && strlen($request->frd_code) ==6 && ctype_alnum($request->frd_code)){
          $check_valid_user = User::where('referal_code',$request->frd_code)->first();
          $check_register = User::where('frd_referral_code',$request->frd_code)->orderBy('id','desc')->first();
          
          if(empty($check_valid_user)){
            return response()->json(array('message' => 'Your Refferal Code is not valid.', 'status' => 'error'), 422);
          }
          else if(!empty($check_register)){

           $startTime = $check_register->created_at->toDateTimeString();
           $finishTime = Carbon::now();
           $totalDuration = $finishTime->diffInSeconds($startTime);
           if($totalDuration < 60){
              return response()->json(array('message' => 'Something went wrong. Please try later.', 'status' => 'error'), 422);
           }
         }
        }

        // if($request->imei_number !=''){
        //   $check_imei_user = User::where('imei_number',$request->imei_number)->first();
        // }else{
        //   $check_imei_user = array();
        // }

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        Mail::to($user)->send(new ValidEmail($user));
       
        //if(Mail::to($user)->send(new ValidEmail($user))){
          
          //$user->name = $request->name;
          //$user->email = $request->email;
          $user->password = Hash::make($request->password);
          $user->referal_code = $this->generateReferalCode(6);
          $user->imei_number = $request->imei_number;
          $user->frd_referral_code = $request->frd_code;
          $user->role_id = '2';
          $user->save();

          $user = $this->getUser($user);

          $link = url("/verifyemail/".$user->id);
          Mail::to($user)->send(new VerifyEmailLink($user, $link));
          return response()->json(array('message' => 'Success'), 200);
        //}else{
        //  return response()->json(array('message' => 'Please enter valid Email.', 'status' => 'error'), 422);
        //}
        
       /* if($user){
          //$link = '(<a href="/verifyemail/'.$user->id.'" target="_blank">Click Here to verfiy email</a>)';
          $link = url("/verifyemail/".$user->id);
          Mail::to($user)->send(new VerifyEmailLink($user, $link));


          // if(!empty($check_valid_user) && empty($check_imei_user)){
              
          //   $getUser = $this->frd_referral_code_fun($check_valid_user->id);
          //   $getUser1 = $this->frd_referral_code_fun($user->id);
          //   $setting = Setting::find(1);

          //   $message1 ='Congratulations! You have earned '.$setting->frd_refferal_coin.' Sweatcoins using our referral program. Refer more & earn more!';
          //   $notification_data = ['description' => $message1, 'title' => 'Walk & Earn', 'type' => 'Reward'];

          //   $android_token = User::where('id',$getUser->id)->where('device_type', '1')->get()->pluck('fcm_token')->toArray();
          //   //$action = "in.hexalab.walkandearn.activity.HomeActivity";
          //   $action = "in.hexalab.walkandearn.activity.RewardActivity";
          //   FcmNotificationController::sendNotification($android_token, $notification_data, $action);
          // }
        } */

       // $access_token = $user->createToken('access token')->accessToken;
       // return response()->json(array('message' => 'Success', 'access_token' => $access_token, 'User' => $user), 200);
        
    }

    public function socialLogin(Request $request)
    {
        $validate = Validator::make($request->all(), [
          'email' => 'required|email',
          'provider' => 'required',
          'provider_id' => 'required'
        ]);

        if($validate->fails()){
          return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
        }

        if(!in_array($request->provider, ['Facebook', 'Google'])){
          return response()->json(array('message' => 'Provider must be Facebook or Google.'), 422);
        }

        $user = User::where('email', $request->email)->first();
        if(!empty($user) && $user->status == '2'){
          return response()->json(array('message' => 'Your account has been blocked. Please contact with Admin at walkearn2019@gmail.com.'), 422);
        }

        $denied_hostnames = array("bcaoo.com", "ixaes.com","net1mail.com","urhen.com","ixaks.com","ymail.com","oqiwq.com");
        foreach ($denied_hostnames as $hn)
        {
            if (strstr($request->email, "@" . $hn))
            {
              return response()->json(array('message' => "Sorry! We don't accept " . $hn . " email addresses.", 'status' => 'error'), 422);
            }
        }

        if($request->frd_code !='' && strlen($request->frd_code) == 6 && ctype_alnum($request->frd_code)){
         
          $check_valid_user = User::where('referal_code',$request->frd_code)->first();
          $check_register = User::where('frd_referral_code',$request->frd_code)->orderBy('id','desc')->first();

          if(empty($check_valid_user)){
            return response()->json(array('message' => 'Your Refferal Code is not valid.', 'status' => 'error'), 422);
          
          }else if(!empty($check_register)){

            $startTime = $check_register->created_at->toDateTimeString();
            $finishTime = Carbon::now();
            $totalDuration = $finishTime->diffInSeconds($startTime);
            if($totalDuration < 60){
              return response()->json(array('message' => 'Something went wrong. Please try later.', 'status' => 'error'), 422);
            }
          }
        }


         // return response()->json(array('message' => $check_valid_user, 'status' => 'error'), 422); exit;
        // if(!empty($check_valid_user) && empty($check_imei_user)){
        //   return response()->json(array('message' => 'ddd', 'status' => 'error'), 422); exit;
        // }else{
        //   return response()->json(array('message' => 'aaa', 'status' => 'error'), 422); exit;
        // }
        if($request->imei_number !=''){
          $check_imei_user = User::where('imei_number',$request->imei_number)->first();
        }else{
          $check_imei_user = array();
        }
        

        $social = Social::where([['provider', $request->provider], ['provider_id', $request->provider_id]])->first();
        if($social){
          $user = User::find($social->user_id);
          if($user){

          foreach($user->tokens as $token){
            $token->revoked = true;
            $token->save();
          }
          $user->makeHidden('tokens'); 

            //$user->image = $request->image;
            $user->name = $request->name;
            $user->status='1';
            $user->email_verified_at= date('Y-m-d H:i:s');
            //$user->referal_code = $this->generateReferalCode(6);
            $user->save();

          }else{
            $user = new User;
            $user->email = $request->email;
            //$user->image = $request->image;
            $user->name = $request->name;
            $user->referal_code = $this->generateReferalCode(6);
            $user->imei_number= $request->imei_number;
            $user->status='1';
            $user->email_verified_at= date('Y-m-d H:i:s');
            $user->frd_referral_code = $request->frd_code;
            $user->role_id = '2';
            $user->save();

            $social->user_id = $user->id;
            $social->save();
          }
        }
        else{
          $user = User::where('email', $request->email)->first();
          if($user){

            foreach($user->tokens as $token){
              $token->revoked = true;
              $token->save();
            }
            $user->makeHidden('tokens'); 
            
            $social = new Social;
            $social->user_id = $user->id;
            $social->provider = $request->provider;
            $social->provider_id = $request->provider_id;
            $social->save();

            //$user->image = $request->image;
            $user->name = $request->name;
            $user->status='1';
            $user->email_verified_at= date('Y-m-d H:i:s');
            $user->save();
          }
          else{
            $user = new User;
            $user->email = $request->email;
            //$user->image = $request->image;
            $user->name = $request->name;
            $user->referal_code = $this->generateReferalCode(6);
            $user->imei_number= $request->imei_number;
            $user->status='1';
            $user->email_verified_at= date('Y-m-d H:i:s');
            $user->frd_referral_code = $request->frd_code;
            $user->role_id = '2';

            Mail::to($user)->send(new ValidEmail($user));
            $user->save();
            $user = $this->getUser($user);

            $social = new Social;
            $social->user_id = $user->id;
            $social->provider = $request->provider;
            $social->provider_id = $request->provider_id;
            $social->save(); 
          }
        }

        /*if(!empty($check_valid_user) && empty($check_imei_user)){
          
          //$getUser = $this->frd_referral_code_fun($check_valid_user->id);
          //$getUser1 = $this->frd_referral_code_fun($user->id);

          $setting = Setting::find(1);

          $message1 ='Congratulations! You have earned '.$setting->frd_refferal_coin.' Sweatcoins using our referral program. Refer more & earn more!';
          $notification_data = ['description' => $message1, 'title' => 'Walk & Earn', 'type' => 'Reward'];

          $android_token = User::where('id',$getUser->id)->where('device_type', '1')->get()->pluck('fcm_token')->toArray();
          //$action = "in.hexalab.walkandearn.activity.HomeActivity";
          $action = "in.hexalab.walkandearn.activity.RewardActivity";
          FcmNotificationController::sendNotification($android_token, $notification_data, $action);
        } */

        $access_token = $user->createToken('access token')->accessToken;
        $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$user->id)->where('scratch_status','2')->first();
        //$paytm_info = PaytmTranscation::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$user->id)->where('status','1')->first();
        $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$user->id)->where('status','!=',3)->first();
        $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
        $paytm_bal= $paytm_info->paytm_bal;

        return response()->json(array('message' => 'Success', 'access_token' => $access_token, 'User' => $user,'total_earning_till_date' => $paytm_bal,'grand_total_bal' => $wallet_info->total_wallet_bal), 200);
    }

    public function signIn(Request $request)
    {
        $validate = Validator::make($request->all(), [
          'email' => 'required|email',
          'password' => 'required'
        ]);

        if($validate->fails()){
          return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
        }
        
        $user = User::where('email', $request->email)->first();

        if($user){

          foreach($user->tokens as $token){
            $token->revoked = true;
            $token->save();
          }
          $user->makeHidden('tokens'); 

          if($user->status !='2'){
            if($user->status==0 && $user->email_verified_at==NULL){
              //$link = '(<a href="/verifyemail/'.$user->id.'" target="_blank">Click Here to verfiy email</a>)';
              $link = url("/verifyemail/".$user->id);
              Mail::to($user)->send(new VerifyEmailLink($user, $link));
              return response()->json(array('message' => 'Please verify your email address. Verfication Link has been sent to your registered email.'), 422);
            }
            if(Hash::check($request->password, $user->password)){

              $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$user->id)->where('scratch_status','2')->first();
              //$paytm_info = PaytmTranscation::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$user->id)->where('status','1')->first();
              $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$user->id)->where('status','!=',3)->first();
              $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
              $paytm_bal= $paytm_info->paytm_bal;

              // if ($user->accessTokens->count() > 0) {
              //   $user->accessTokens()->delete();
              // }

              $access_token = $user->createToken('access token')->accessToken;
              return response()->json(array('message' => 'Success', 'access_token' => $access_token, 'User' => $user,'total_earning_till_date' => $paytm_bal,'grand_total_bal' => $wallet_info->total_wallet_bal), 200);
            }
            return response()->json(array('message' => 'Wrong password.'), 422);
          }else{
            return response()->json(array('message' => 'Your account has been blocked. Please contact with Admin at walkearn2019@gmail.com.'), 403);
          }
        }
        return response()->json(array('message' => 'Email not found.'), 404);
    }

    public function getUser($user)
    {
      return User::find($user->id);
    }

    public function forgotPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
          'email' => 'required|email'
        ]);

        if($validate->fails()){
          return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
        }

        $user = User::where('email', $request->email)->first();
        if($user){
          $user->otp = $this->generateOtp();
          $user->save();
          Mail::to($user)->send(new VerifyEmailOtp($user, $user->otp));
          return response()->json(array('message' => 'Otp has been sent successfully to email.'), 200);
        }
        return response()->json(array('message' => 'Email not found.'), 404);
    }

    public function generateOtp()
    {
      return mt_rand(100000, 999999);
    }

    public function resetPassword(Request $request)
    {
        $validate = Validator::make($request->all(), [
          'email' => 'required|email',
          'otp' => 'required',
          'password' => 'required|min:6'
        ]);

        if($validate->fails()){
          return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
        }

        $user = User::where('email', $request->email)->first();
        if($user){
          if($user->otp == $request->otp){
            $user->otp = null;
            $user->password = Hash::make($request->password);
            $user->save();

            $access_token = $user->createToken('access token')->accessToken;
            return response()->json(array('message' => 'Success', 'access_token' => $access_token, 'User' => $user), 200);
          }
          return response()->json(array('message' => 'Invalid otp.'), 422);
        }
        return response()->json(array('message' => 'Email not found.'), 404);
    }

    public function generateReferalCode($strength){

      $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $random_string = '';
      $input_length = strlen($permitted_chars);
      for($i = 0; $i < $strength; $i++) {
        $random_character = $permitted_chars[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
      }
      return $random_string;
    }


    public function frd_referral_code_fun($user_id){ 

      if($user_id){

        $setting = Setting::find(1);
        $check_user = RewardCoin::where('user_id',$user_id)->where('reward_type','1')->first();

        $reward_coin = new RewardCoin;
        if(empty($check_user)){
          $reward_coin->coins = $setting->frd_refferal_coin;
        }else{
          $reward_coin->coins = floor($setting->frd_refferal_coin/2);
        }

        $reward_coin->reward_type = '1';
        $reward_coin->user_id = $user_id;
        $reward_coin->save();

        if($reward_coin->id > 0){

          $user = User::find($user_id);
          $getRewardCoin = RewardCoin::selectRaw('SUM(coins) as total_coin')->where('user_id',$user_id)->first();
          //return response()->json(array('message' => $getRewardCoin,'status' => 'success'), 200);
          //$user->available_coins = ($user->available_coins + $getRewardCoin->total_coin);
         // $user->available_coins = ($user->available_coins + $setting->frd_refferal_coin);
          $user->available_coins = ($user->available_coins + $reward_coin->coins);
          $user->save();
          return $user;
        }
      }
    }   
}
