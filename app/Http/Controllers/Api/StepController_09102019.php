<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Auth;
use Validator;
use App\User;
use App\Step;
use App\Reward;
use App\Wallet;
use App\PaytmTranscation;
use App\PaytmRequest;
use App\RewardCoin;
use App\Setting;
use App\LuckyCoupon;

use App\Http\Controllers\Api\FcmNotificationController as FcmNotificationController;

/*******
Formula: 
100 steps = 1 coins,
1000 steps = 10 coins,
10000 steps = 100 coins
******/

class StepController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
          $this->user = Auth::user();
          return $next($request);
        });

        $this->now = Carbon::now();
    }

    public function add_steps(Request $request){
      // if (Auth::check()) {
      //       // user is authenticated
      //   return response()->json(array('message' => $user), 403); exit;
      //   } else {
      //       return response()->json(array('message' => 'ss'), 401); exit;
      //   }exit;
      $user = User::find($this->user->id);  // return response()->json(array('message' => $user), 401); exit;
      if($user){
        $validate = Validator::make($request->all(), [
          'date' => 'required',
          ]);

          if($validate->fails()){
            return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
          }
          
          $check_steps_count = Step::where('user_id',$this->user->id)->orderBy('id','desc')->count();
          // if(empty($check_steps_count)){
          //   $count_step = 0;
          // }else{
          //   //$count_step = $check_steps_count->totalsteps;
          //   $totalsteps = Step::selectRaw('SUM(steps) as totalsteps')->where('user_id',$this->user->id)->orderBy('id','desc')->first();
          //   $count_step = $totalsteps;
          // }

          $stepsHistories = $request->stepsHistories;

          foreach ($stepsHistories as $key => $value) {
            
            $hour = $value['hour']; 
            $steps = $value['steps'];
            $timestamp = $value['timestamp'];

            $check_list_info = Step::where('timestamp','!=',$timestamp)->where('hour',$hour)->where('sync_date',date('Y-m-d',strtotime($request->date)))->where('user_id',$this->user->id)->orderBy('id','desc')->first();

            $check_list_info1 = Step::where('timestamp',$timestamp)->where('hour',$hour)->where('sync_date',date('Y-m-d',strtotime($request->date)))->where('user_id',$this->user->id)->orderBy('id','desc')->first();

              if($check_list_info){

                $temp_step = $check_list_info->steps;

                $check_list_info->steps = ($steps >15000) ? 15000 : $steps;
                $check_list_info->timestamp = $timestamp;
               // $count_step = $check_list_info->totalsteps = $count_step + $steps;
                //$count_step = $check_list_info->totalsteps = $steps;
                //$count_step = $check_list_info->totalsteps = $count_step + ($steps-$temp_step);
                $check_list_info->updated_at = date('Y-m-d H:i:s');
                $check_list_info->save();

              }elseif(empty($check_list_info1)){

                $list_data = new Step;
                $list_data->hour = $hour;
                $list_data->steps = ($steps >15000) ? 15000 : $steps;
                $list_data->timestamp = $timestamp;
                $list_data->user_id = $this->user->id;
               // $count_step = $list_data->totalsteps = $count_step + $steps;
               // $count_step = $list_data->totalsteps = $steps;
                $list_data->sync_date = date('Y-m-d',strtotime($request->date));
                $list_data->created_at = date('Y-m-d H:i:s');
                $list_data->updated_at = date('Y-m-d H:i:s');
                $list_data->save();
              }


              $totalsteps = Step::selectRaw('SUM(steps) as totalsteps')->where('user_id',$this->user->id)->orderBy('id','desc')->first();
              $count_step = $totalsteps->totalsteps;
              $wallet = Wallet::join('rewards','rewards.id','=','wallets.reward_id')->selectRaw("SUM(rewards.value) as redeem_coins")->where('user_id',$this->user->id)->first();
              $total_steps_data = $count_step;
              
              if($total_steps_data >0){

                $user->total_steps = $total_steps_data;
               // $totalcoins =round($total_steps_data/100, 0, PHP_ROUND_HALF_DOWN);
                $totalcoins =floor($total_steps_data/100);
                /*$totalCoinsDay = 0;
                //if user has completed 20,000 steps coin should be added 200 coins if exceeded 20000 steps in a day then 200 coin will add in their wallet.
                $check_step_limit = Step::selectRaw("SUM(steps) as per_day_steps")->where('user_id',$this->user->id)->groupBy('sync_date')->orderBy('id','desc')->get();
                foreach ($check_step_limit as $key => $value) {
                  if($value->per_day_steps <=20000){
                      $totalCoinsDay = $totalCoinsDay + floor($value->per_day_steps/100);
                  }else{
                      $totalCoinsDay = $totalCoinsDay + 200;
                  }
                }
                $totalcoins = $totalCoinsDay; */
                $user->redeem_coins = $wallet->redeem_coins;
                $getRewardCoin = RewardCoin::selectRaw('SUM(coins) as total_coin')->where('user_id',$this->user->id)->first();
                
                  if($totalcoins >=0){
                    $before_coin = $user->available_coins;
                    $after_coin = $user->available_coins = ($getRewardCoin->total_coin+$totalcoins) - $user->redeem_coins;
                     
                  }else{
                    $before_coin = $user->available_coins;
                    $after_coin = $user->available_coins = ($getRewardCoin->total_coin) - $user->redeem_coins;
                  }
                  $this->sendNoticationMilestone($before_coin,$after_coin,$this->user->id); 
                  $user->save();
              }
            }               
          $todayCoin = $this->getPerdayCoinByuser($this->user->id,date('Y-m-d'));

          $check_imei_user = User::where('imei_number',$this->user->imei_number)->count();
          $check_valid_user = User::where('referal_code',$this->user->frd_referral_code)->first();

          // if(isset($this->user->frd_referral_code) && !isset($check_steps_count) && ($check_imei_user==1) && !empty($check_valid_user)){ 
          /*  if(isset($this->user->frd_referral_code) && !empty($check_valid_user) && ($check_imei_user==1) && ($check_steps_count == 0)){
            $getUser = $this->frd_referral_code_fun($check_valid_user->id);
            $getUser1 = $this->frd_referral_code_fun($this->user->id);

            $setting = Setting::find(1);

            $message1 ='Congratulations! You have earned '.$getUser->frd_reward_coin.' Sweatcoins using our referral program. Refer more & earn more!';
            $notification_data = ['description' => $message1, 'title' => 'Walk & Earn', 'type' => 'Reward'];

            $android_token = User::where('id',$getUser->id)->where('device_type', '1')->get()->pluck('fcm_token')->toArray();
            $action = "in.hexalab.walkandearn.activity.RewardActivity";
            FcmNotificationController::sendNotification($android_token, $notification_data, $action);
          } */

          return response()->json(array('message' => 'List has been added successfully.','user' => $user,'todayCoin' => $todayCoin), 200);
      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }

    public function fetchStep(){

      //$date = date('Y-m-d', strtotime($request->date));
      
      $user = User::find($this->user->id); 
      
      if(!empty($user)){
          
          $list_date_info = Step::where('user_id',$user->id)->orderBy('id','desc')->first();

          if(!empty($list_date_info)){ 
              
            return response()->json(array('status' => 'success',"last_list" => $list_date_info), 200);
            
          }else{
            return response()->json(array('status' => 'error','message' => 'List not found.'), 403);
          }
          
      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }

    public function fetchAllListByUser(){
      
      $user = User::find($this->user->id);
      
      if(!empty($user)){

        $list_date_info = Step::selectRaw('*,DATE_FORMAT(sync_date, "%d-%m-%Y") as sync_date1')->where('user_id',$user->id)->groupBy('sync_date')->orderBy('id', 'desc')->get();

        foreach ($list_date_info as $key => $value) {
          
          $value->stepHistories = Step::where('user_id',$user->id)->where('sync_date',$value->sync_date)->get();
        }
        return response()->json(array('status' => 'success',"list_data" => $list_date_info), 200);

      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }
     
    /* 
    public function getMonthList($num){

      $user = User::find($this->user->id);
      
      if(!empty($user)){

        $current_date = date('Y-m-d');
        $lastDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d"))) . "-".$num." month" ) );

        $list_date_info = ListDate::with('listinfo')->whereBetween('date',[$lastDate, $current_date])->get();
        
        if(!empty($list_date_info)){
          return response()->json(array('status' => 'success','current_date' => $current_date,'lastDate' => $lastDate, 'list_data' =>$list_date_info), 200);
        }else{
          return response()->json(array('status' => 'error','message' => 'List not found.'), 403);
        }

      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }
    */

    public function rewardlist(){
      
      if($this->user->id){
        $reward = Reward::where('status','1')->orderBy('value','asc')->get();
        $available_coins= $this->user->available_coins;

        $setting = Setting::find(1);

      //case 500 coins can redem in the week
      $monday = date( 'Y-m-d', strtotime( "monday this week" )); 
      $sunday = date( 'Y-m-d', strtotime( 'sunday this week' )); 

      $check_wallet_info =  Wallet::selectRaw('SUM(rewards.value) as weekly_redem_coin')->leftJoin('rewards', 'wallets.reward_id', '=', 'rewards.id')->where('user_id',$this->user->id)->whereBetween('redeem_date', [$monday, $sunday])->first();

      $check_timestamp = Wallet::where('user_id',$this->user->id)->whereDate('redeem_date',date('Y-m-d'))->orderBy('id','desc')->first();
      if(!empty($check_timestamp)){
        $startTime = strtotime(date('H:i:00',strtotime($check_timestamp->created_at)));
        $timediff = strtotime(date('H:i:00')) - $startTime;
      }
      

      foreach ($reward as $key => $value) {

        //$check_count = 500 - $check_wallet_info->weekly_redem_coin;
        $check_count = ($check_wallet_info->weekly_redem_coin <= $setting->weekly_redem_coin) ? $setting->weekly_redem_coin - $check_wallet_info->weekly_redem_coin : 0;
        
        if($available_coins >= $value->value){ 
        //if($check_wallet_info->weekly_redem_coin >= $value->value){
          
          //$value->note = $value->value." has been deducted."; 
          if($value->value <= $check_count){

             if(!empty($check_timestamp)){
              $startTime = strtotime(date('H:i:00',strtotime($check_timestamp->created_at)));
              $timediff = strtotime(date('H:i:00')) - $startTime;

              if($timediff >3600){
                $value->weekly_status = true;
                $value->weekly_label = 'success';
                $value->reward_status = true;
                $value->note = "You are eligible to claim this reward. Claim Now!";
              }else{
                $value->weekly_status = false;
                $value->weekly_label = 'success';
                $value->reward_status = false;
                
                $value->note = "You can claim this reward after one hour.";
              }
             }else{
                $value->weekly_status = true;
                $value->weekly_label = 'success';
                $value->reward_status = true;
                $value->note = "You are eligible to claim this reward. Claim Now!";
             }

                // $value->weekly_status = true;
                // $value->weekly_label = 'success';
                // $value->reward_status = true;
                // $value->note = "You are eligible to claim this reward. Claim Now!";

              // }else{
              //   $value->weekly_status = false;
              //   $value->weekly_label = 'success';
              //   $value->reward_status = false;
              //   $value->note = "You can claim this reward after one hour.";
              // }    

          }else{

              $value->weekly_status = false;
              $html =  $check_count > 0 ? ','.($check_count).' coins left for this week.' : '.';

              $value->weekly_label = 'You can claim upto a maximum of '.$setting->weekly_redem_coin.' coins per week. You have already claimed '.$check_wallet_info->weekly_redem_coin.' coins'.$html;

               $value->reward_status = false;
               $value->note = $value->weekly_label ;

          }
        }else{
        
          if($value->value > $check_count){

            $value->reward_status = false;
            $value->weekly_status = false;
            $html =  $check_count > 0 ? ','.($check_count).' coins left for this week.' : '.';
            $value->weekly_label = 'You can claim upto a maximum of '.$setting->weekly_redem_coin.' coins per week. You have already claimed '.$check_wallet_info->weekly_redem_coin.' coins'.$html;
            $value->note = $value->weekly_label ;

          }else{
            $value->coinstoredeem = $value->value - $available_coins;
            $value->reward_status = false;
            $value->weekly_status = false;
            $value->weekly_label = 'not sufficient balance.';
            $value->note = 'You can claim upto a maximum of '.$setting->weekly_redem_coin.' coins per week. Earn '.$value->coinstoredeem." more coins to get this reward!";
          }  
        }  

        $temp_value =10;
        if($value->value ==100){
          $max_limit = 10*$temp_value;
          $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
        }
        if($value->value ==200){
          $max_limit = 20*$temp_value;
          $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
        }
        if($value->value ==300){
          $max_limit = 30*$temp_value;
          $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
        }
        if($value->value ==500){
          $max_limit = 50*$temp_value;
          $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
        }
        if($value->value ==1000){
          $max_limit = 100*$temp_value;
          $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
        }
        if($value->value ==700){
          $max_limit = 70*$temp_value;
          $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
        }
          
      }

      $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$this->user->id)->where('scratch_status','2')->first();
      //$paytm_info = PaytmTranscation::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$this->user->id)->where('status','1')->first();
      $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$this->user->id)->where('status','!=',3)->first();
      $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
      $paytm_bal= $paytm_info->paytm_bal;

      $user = User::find($this->user->id);
      //$user->available_coins = $user->available_coins > 0 ? $user->available_coins : 0;

      return response()->json(array('status' => 'success', 'reward_list' => $reward, 'wallet_balance' => $wallet_balance,'total_earning_till_date' => $paytm_bal,'grand_total_bal' => $wallet_info->total_wallet_bal,'user' => $user,'week_start' => $monday,'week_end'=>$sunday,'week_redeem_limit' => $setting->weekly_redem_coin), 200);
      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }   
    }

    public function getAmountScratch(Request $request){
      
      if($this->user->id){

        $slabs_rs =0;
        $reward_id = $request->reward_id;
        $reward_info = Reward::find($reward_id);

        $check_user_wallet = Wallet::where('user_id',$this->user->id)->first();

        if(empty($check_user_wallet)){

          if($reward_id==1){
           //$slabs_rs = 5;
           $slabs_rs = 2;
          }

          if($reward_id==2){
          //$slabs_rs = 6;
          $slabs_rs = 3;
          }

          if($reward_id==3){
          //$slabs_rs = 10;
            $slabs_rs = 5;
          }

          if($reward_id==4){
          //$slabs_rs = 14;
            $slabs_rs = 7;
          }

          if($reward_id==5){
          //$slabs_rs = 20;
          $slabs_rs = 10;
          }

          if($reward_id==6){
         // $slabs_rs = 17;
            $slabs_rs = 9;
          }

        }else{ 

          //slabs if reward id 1 means 100 coins random rupees 1-5
          if($reward_id==1){

          $slabs_rs = $this->generate_slab(1,3);
          }

          //slabs if reward id 2 means 200 coins random rupess 5-10
          if($reward_id==2){
          $slabs_rs = $this->generate_slab(2,4);
          }

          //slabs if reward id 3 means 300 coins random rupess 6-15
          if($reward_id==3){
          $slabs_rs = $this->generate_slab(3,6);
          }

          //slabs if reward id 4 means 500 coins random rupess 11-25
          if($reward_id==4){
            $slabs_rs = $this->generate_slab(3,8);
          }

          //slabs if reward id 5 means 1000 coins random rupess 11-25
          if($reward_id==5){
            $slabs_rs = $this->generate_slab(5,12);
          }

          //slabs if reward id 6 means 700 coins random rupess 5-19
          if($reward_id==6){
            $slabs_rs = $this->generate_slab(4,10);
            //$slabs_rs = 9;
          }
        } 

        if($this->user->available_coins > 0){

          $setting = Setting::find(1);

          //case 500 coins can redem in the week
          $monday = date( 'Y-m-d', strtotime( "monday this week" )); 
          $sunday = date( 'Y-m-d', strtotime( 'sunday this week' )); 

          $check_wallet_info =  Wallet::selectRaw('SUM(rewards.value) as weekly_redem_coin')->leftJoin('rewards', 'wallets.reward_id', '=', 'rewards.id')->where('user_id',$this->user->id)->whereBetween('redeem_date', [$monday, $sunday])->first();

          if($check_wallet_info->weekly_redem_coin == $setting->weekly_redem_coin){
            return response()->json(array('message' => 'Your limit has completed for this week.','status' => 'error'), 422);
          }

          $check_count = ($check_wallet_info->weekly_redem_coin <= $setting->weekly_redem_coin) ? $setting->weekly_redem_coin - $check_wallet_info->weekly_redem_coin : 0;

          if($check_count >= $reward_info->value){ 
            
            $wallet = new Wallet;
            $wallet->rupees = $slabs_rs;
            $wallet->reward_id = $reward_id;
            $wallet->user_id = $this->user->id;
            $wallet->redeem_date = date('Y-m-d H:i:s');
            $wallet->created_at = date('Y-m-d H:i:s');
            $wallet->updated_at = date('Y-m-d H:i:s');
            $wallet->save();

            if($slabs_rs  > 0 && $wallet->id > 0){

              $wallet_info = Wallet::join('rewards','rewards.id','=','wallets.reward_id')->selectRaw("SUM(rewards.value) as redeem_coins")->where('user_id',$this->user->id)->first();

              $getRewardCoin = RewardCoin::selectRaw('SUM(coins) as total_coin')->where('user_id',$this->user->id)->first();

              $user = User::find($this->user->id);  
              //$totalcoins =round($user->total_steps/100, 0, PHP_ROUND_HALF_DOWN);
              $totalcoins =floor($user->total_steps/100);
              $user->redeem_coins = $wallet_info->redeem_coins;

              $get_redeem_coin = $getRewardCoin->total_coin + $totalcoins;
              //$user->available_coins = $get_redeem_coin - $user->redeem_coins;
              //$user->available_coins = $get_redeem_coin - $reward_info->value;
              $user->available_coins = $get_redeem_coin - $wallet_info->redeem_coins;
              $user->save();

              return response()->json(array('message' => 'Reward has been scratched successfully.','status' => 'success','rupees' => $slabs_rs,'wallet_id' => $wallet->id,'user' => $user), 200);
            }
          }else{
            return response()->json(array('message' => 'You have insufficient coins.','status' => 'error'), 422);
          }
          // else{
          //   return response()->json(array('message' => 'Reward not found.','status' => 'error'), 422);
          // }
        }else{
          return response()->json(array('message' => 'You have insufficient coins.','status' => 'error'), 422);
        }

      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }

    public function getCoinClaim(Request $request){

      if($this->user->id){

        $validate = Validator::make($request->all(), [
          'wallet_id' => 'required',
          'rupees' => 'required',
          ]);

          if($validate->fails()){
            return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
          }

        $reward_id = $request->reward_id;

        $wallet = Wallet::find($request->wallet_id);
        //$wallet->rupees = $request->rupees;
        //$wallet->reward_id = $reward_id;
        //$wallet->user_id = $this->user->id;
        $wallet->scratch_status = '2';
       // $wallet->redeem_date = date('Y-m-d H:i:s');
        $wallet->updated_at = date('Y-m-d H:i:s');
        $wallet->save();

        if($wallet->id){
          $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$this->user->id)->where('scratch_status','2')->first();
          $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$this->user->id)->where('status','!=',3)->first();
          $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
                $paytm_bal= $paytm_info->paytm_bal;


          return response()->json(array('message' => 'Reward has been claimed successfully.','status' => 'success','reward' =>$wallet,'wallet_balance' => $wallet_balance,'total_earning_till_date' => $paytm_bal,'grand_total_bal' => $wallet_info->total_wallet_bal), 200);
        }else{
          return response()->json(array('message' => 'Reward has not been claimed.','status' => 'error'), 422);
        }

      }else{
        return response()->json(array('message' => 'User not found.'), 404);
      }
    }

    public function generate_slab($first,$last){

      return rand($first,$last);
    }

    public function rewardHistory(Request $request){

      if($this->user->id){

        $offset = $request->offset;
        $limit= $request->limit;
        
        $reward = Wallet::selectRaw('wallets.*,rewards.title,rewards.value')->join('rewards','rewards.id','=','wallets.reward_id')->where('user_id',$this->user->id)->orderBy('id','desc')->limit($limit)->offset($offset)->get();
        if($reward){

          $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$this->user->id)->where('scratch_status','2')->first();
          //$paytm_info = PaytmTranscation::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$this->user->id)->where('status','1')->first();
          $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$this->user->id)->where('status','!=',3)->first();
          $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
          $paytm_bal= $paytm_info->paytm_bal;

          $temp_value =10;
          foreach ($reward as $key => &$value) {
            if($value->value ==100){
              $max_limit = 10*$temp_value;
              $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
            }
            if($value->value ==200){
              $max_limit = 20*$temp_value;
              $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
            }
            if($value->value ==300){
              $max_limit = 30*$temp_value;
              $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
            }
            if($value->value ==500){
              $max_limit = 50*$temp_value;
              $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
            }
            if($value->value ==1000){
              $max_limit = 100*$temp_value;
              $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
            }
            if($value->value ==700){
              $max_limit = 70*$temp_value;
              $value->description = 'Earn upto ₹'.$max_limit.' from Walk & Earn.';
            }
          }

            return response()->json(array('reward'=> $reward,'status' => 'success','wallet_balance' => $wallet_balance,'total_earning_till_date' => $paytm_bal,'grand_total_bal' => $wallet_info->total_wallet_bal), 200);
        }else{
          return response()->json(array('message' => 'Reward not found.','status' => 'error'), 422);
        }    
      }else{
        return response()->json(array('message' => 'User not found.','status' => 'error'), 404);
      }
    }

    public function walletHistory(Request $request){

      if($this->user->id){

          $offset = $request->offset;
          $limit= $request->limit;
          $status=$request->status;

           $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$this->user->id)->where('scratch_status','2')->first();

           //$paytm_info = PaytmTranscation::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$this->user->id)->where('status','1')->first();
            $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$this->user->id)->where('status','!=',3)->first();
           
           if(!empty($wallet_info)){
                $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
                $paytm_bal= $paytm_info->paytm_bal;

                
                  $paytm_txn_info = PaytmRequest::where('user_id',$this->user->id)->where('status',$status)->limit($limit)->offset($offset)->get();

                foreach ($paytm_txn_info as $key => $value) {
                  $value['order_num'] = 'WER'.str_pad($value['order_id'], 10, 0, STR_PAD_LEFT );
                }
                
                return response()->json(array('status' => 'success','wallet_balance' => $wallet_balance,'total_earning_till_date' => $paytm_bal,'transcation_list' => $paytm_txn_info,'grand_total_bal' => $wallet_info->total_wallet_bal), 200);
           }else{
              return response()->json(array('message' => 'Wallet balance not found.','status' => 'error'), 422);
           }
      }else{
        return response()->json(array('message' => 'User not found.','status' => 'error'), 404);
      }
    }

    public function getPerdayCoinByuser($user_id,$date){
        
        //$perdayCoins =0;
        $steps_info = Step::selectRaw('SUM(steps) as totaldaysteps')->where('user_id',$user_id)->where('sync_date',$date)->orderBy('id','desc')->first();
        if(!empty($steps_info)){
          //$perdayCoins = round($steps_info['totaldaysteps']/100, 0, PHP_ROUND_HALF_DOWN);
          $perdayCoins = floor($steps_info['totaldaysteps']/100);
        }

        return $perdayCoins;
    }

    public function getPerdayStepsByuser(){
        
      //$perdayCoins =0;
      if($this->user->id){

      $user_id = $this->user->id;
      $date = date('Y-m-d');
      $steps_info = Step::selectRaw('SUM(steps) as totaldaysteps,timestamp')->where('user_id',$user_id)->where('sync_date',$date)->orderBy('id','desc')->first();
        if(!empty($steps_info)){
          //$perdayCoins = round($steps_info['totaldaysteps']/100, 0, PHP_ROUND_HALF_DOWN);
          //$perdayCoins = floor($steps_info['totaldaysteps']/100);
          return response()->json(array('message' => 'Steps got it.','today_total_steps' => $steps_info['totaldaysteps'], 'last_timestamp' => $steps_info['timestamp'],'status' => 'success'), 200);
        }else{
          return response()->json(array('message' => 'No Steps Here.','status' => 'error'), 404);
        }
      }else{
        return response()->json(array('message' => 'User not found.','status' => 'error'), 404);
      }        
    }

    public function paytmRequest(Request $request){

      if($this->user->id){
        $user = User::find($this->user->id);

       // return response()->json(array('message' => 'Please update to the latest version of the app from Play Store and request your withdrawal again.','status' => 'error'), 403); exit;
        $paytm_request_info = PaytmRequest::where('user_id',$user->id)->where('status','1')->orderBy('id','desc')->first();
        $paid_paytm_request = PaytmRequest::where('user_id',$user->id)->where('status','2')->orderBy('id','desc')->first();

        if(!empty($user) && $user->status == '2'){
          return response()->json(array('message' => 'Your account has been blocked. Please contact with Admin at walkearn2019@gmail.com.'), 422);

        }elseif(!empty($paytm_request_info)){
          return response()->json(array('message' => 'Your Request of Ref Id: #'.'WER'.str_pad($paytm_request_info->order_id, 10, 0, STR_PAD_LEFT ).' is in progress. You can request one withdrawal at a time. Please try again later.'), 403);
        }else{

          $validate = Validator::make($request->all(), [
          'amount' => 'required|not_in:0',
          'paytm_mobile_number' => 'required|min:11|numeric',
          'qr_code' => 'required',
          ]);

          if($validate->fails()){
            return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 403);
          }

          $walletArr = $this->wallet_bal(); 

          //$temp = $walletArr['check_amt'] > $request->amount ? ($walletArr['check_amt'] - $request->amount) : $walletArr['check_amt'];
          if(isset($paid_paytm_request) && $paid_paytm_request->id && $request->amount < 20){
            return response()->json(array('message' => 'Minimum withdrawal amount should be Rs.20.','status' => 'error'), 403);
          }
          //return response()->json(array('message' => $walletArr['check_amt'],'status' => 'aa'), 200); exit;
          
           //if($request->amount <= $walletArr['check_amt'] && $request->amount >=20){
          if($request->amount <= $walletArr['check_amt'] && $request->amount > 0){


                $paytm_request = new PaytmRequest;
                $paytm_request->user_id = $this->user->id;
                $paytm_request->amount = (int)$request->amount;
                $paytm_request->paytm_mobile_number = $request->paytm_mobile_number;
                $paytm_request->datetime = date('Y-m-d H:i:s');
                $paytm_request->created_at = date('Y-m-d H:i:s');
                $paytm_request->updated_at = date('Y-m-d H:i:s');

                if($request->qr_code){
                  $image_url = HelperController::imageUpload($request->qr_code, 'qr_code');
                  if($image_url){
                    $paytm_request->qr_code = url($image_url);
                  }
                }

                $user = User::find($this->user->id);
                $user->phone_number = $paytm_request->paytm_mobile_number;
                $user->qr_code = url($image_url);
                $user->save();
                $getOrder = $this->getOrderNumber();
                $paytm_request->order_id = $getOrder['order'];
                $paytm_request->save();

                if($paytm_request->id){
                  $walletArr1 = $this->wallet_bal();
                  $paytm_request->order_num = $getOrder['order_num'];

                    return response()->json(array('message' => 'Paytm request has been added successfully.','status' => 'success','paytm_request'=> $paytm_request,'wallet_data' => $walletArr1,'wallet_balance' => $walletArr1['wallet_balance'],'total_earning_till_date' => $walletArr1['paytm_bal'],'grand_total_bal' => $walletArr1['total_wallet_bal'],'order_num' => $getOrder['order_num']), 200);
                }else{
                    return response()->json(array('message' => 'Something went wrong.','status' => 'error'), 403);
                }

           }else{
              return response()->json(array('message' => 'Minimum withdrawal amount should be 60% of Available Wallet balance.','status' => 'error'), 403);
           }
        }

      }else{
        return response()->json(array('message' => 'User not found.','status' => 'error'), 404);
      }
    }

    public function paytmRequest111(){

      require_once("encdec_paytm.php");

      define("MERCHANT_MID", "xxxxxxxxxxxxxxxxxxxx");
      define("MERCHANT_KEY", "xxxxxxxxxxxxxxxx");

      $paytmParams = array();
      $paytmParams["body"] = array(
      "merchantRequestId" => "xxxxxxxxxxxxxxx",
      "mid" => MERCHANT_MID,
      "linkName" => "ProTest",
      "linkDescription" => "this is only testing link...",
      "linkType" => "FIXED",
      "amount" => "xxx",
      "expiryDate" => "xx/xx/xxxx",
      "isActive" => "true",
      "sendSms" => "true",
      "sendEmail" => "true",
      "customerContact" => array(
      "customerName" => "Paytm Test",
      "customerEmail" => "xxxxxxxxxxxxxxxxxxxx",
      "customerMobile" => "xxxxxxxxx"
      )
      );
      $checksum = getChecksumFromString(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), MERCHANT_KEY);
      $paytmParams["head"] = array(
      "timestamp" => time(),
      "clientId" => "xxx",
      "version" => "v1",
      "channelId" => "WEB",
      "tokenType" => "xxx",
      "signature" => $checksum
      );

      $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);

      // for staging
      $url = "https://securegw-stage.paytm.in/link/create";
      // for production
      // $url = "https://securegw.paytm.in/link/create";

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
      $response = curl_exec($ch);
    }  

    public function wallet_bal(){

      $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$this->user->id)->where('scratch_status','2')->first();

      //$paytm_info = PaytmTranscation::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$this->user->id)->where('status','1')->first();
      $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$this->user->id)->where('status','!=',3)->first();

      $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);

      // amount should be transfered 70% of wallet balance and greater than 20 rupees also

     // $check_amt = round(($wallet_balance * .8), 0, PHP_ROUND_HALF_DOWN);
      $check_amt = floor(($wallet_balance * .6));

      //$response = array();

      $response = array('wallet_balance' => $wallet_balance,
                        'check_amt' => $check_amt,
                        'paytm_bal' => $paytm_info->paytm_bal,
                        'total_wallet_bal' => $wallet_info->total_wallet_bal
                      );
      return $response;
    }

    public function rewardads(){ 

      if($this->user->id){

        $setting = Setting::find(1);
        $last_time_rewardAds= RewardCoin::where('user_id',$this->user->id)->where('reward_type','2')->orderBy('id','desc')->first();
        //$time_diff = (strtotime(date('Y-m-d H:i:s')) - strtotime($last_time_rewardAds->created_at->toDateTimeString()));
        //$check_time = date('Y-m-d H:i:s',$time_diff);
        if(!empty($last_time_rewardAds)){
          $startTime = $last_time_rewardAds->created_at->toDateTimeString();
          $finishTime = $this->now;
          $totalDuration = $finishTime->diffInMinutes($startTime);

          $check_time = $totalDuration + 1;
          if($check_time < $setting->reward_ads_time_interval){
           return response()->json(array('message' => 'Reward has not added.','status' => 'error','check_time'=>$totalDuration,'last_time' => $finishTime,'startTime'=>$startTime ), 422);
          }
        }
        
        $reward_coin = new RewardCoin;
        if($this->user->id == 30349){
          $reward_coin->coins = 500;
        }else{
          $reward_coin->coins = $setting->reward_ads_coin;
        }
        
        $reward_coin->reward_type = '2';
        $reward_coin->user_id = $this->user->id;
        $reward_coin->save();

        if($reward_coin->id > 0){

          $user = User::find($this->user->id);
          $getRewardCoin = RewardCoin::selectRaw('SUM(coins) as total_coin')->where('user_id',$this->user->id)->first();
          //return response()->json(array('message' => $getRewardCoin,'status' => 'success'), 200);
          $before_coin = $user->available_coins;
          if($this->user->id == 30349){
          $user->available_coins = round(($user->available_coins + $reward_coin->coins));
          }else{
            $user->available_coins = round(($user->available_coins + $setting->reward_ads_coin));
          }
          
          $user->save();
          $after_coin = $user->available_coins;
          $this->sendNoticationMilestone($before_coin,$after_coin,$this->user->id);


          return response()->json(array('message' => 'You have won '.$setting->reward_ads_coin.' coins in your Walk & Earn wallet.','status' => 'success','user' => $user,'ads_coin' => $setting->reward_ads_coin), 200);

         }else{
            return response()->json(array('message' => 'Reward has not added.','status' => 'error'), 422);
         }

      }else{
        return response()->json(array('message' => 'User not found.','status' => 'error'), 404);
      }
    }  

    public function getUser(){

      if($this->user->id){
        
        $user = User::find($this->user->id); 

        return response()->json(array('status' => 'success','user' => $user,'condition' => '<ol><li> The amount should be a maximum of 60 percent of the wallet balance.</li><li> It may take up to 24-72 working hours for the amount to get credited in your above mentioned PayTm account.</li><ol>'), 200);
      }else{
        return response()->json(array('message' => 'User not found.','status' => 'error'), 404);
      }
    }

    public function getOrderNumber(){

      $check_order = PaytmRequest::orderBy('id','desc')->first();
      $count_num =1;
      $order = $check_order->order_id + $count_num;
      $order_num = 'WER'.str_pad($order, 10, 0, STR_PAD_LEFT );
      $res = array('order'  => $order,'order_num' => $order_num);
      return $res;
    }

    public function sendNoticationMilestone($before_coin,$after_coin,$user_id){

      $milestone = array(100,200,300,500,600,700,800,1000,1100,1200,1300,1500);

      for($i=0;$i<count($milestone);$i++){ 

          if(($before_coin < $milestone[$i]) && ($milestone[$i]<=$after_coin)){ 
            $message1 = 'You have earned enough coins to avail reward. Scratch now and claim your reward!';
            $notification_data = ['description' => $message1, 'title' => 'Walk & Earn', 'type' => 'Reward'];
            $action = "in.hexalab.walkandearn.activity.RewardActivity";
            $android_token = User::where('id',$user_id)->where('device_type', '1')->get()->pluck('fcm_token')->toArray();
            FcmNotificationController::sendNotification($android_token, $notification_data, $action);
        }
      }
    }

    public function frd_referral_code_fun($user_id){ 

      if($user_id){
        $user = User::find($user_id);

       // $check_user = User::where('frd_referral_code',$user->frd_referral_code)->orWhere('referal_code',$user->frd_referral_code)->count();

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
          $user->frd_reward_coin = $reward_coin->coins;
          return $user;
        }
      }
    }


  public function lucky_coupons(Request $request){
    
    if($this->user->id){
      $setting = Setting::find(1);
      if($request->lucky_coin > $setting->lucky_max){
        return response()->json(array('message' => 'Coins are incorrect.','status' => 'error'), 403);
      }
      $date = date('Y-m-d');
      $check_user = RewardCoin::where('user_id',$this->user->id)->where('reward_type','3')->whereDate('created_at','=',$date)->orderBy('id','desc')->first();

      if(empty($check_user)){

        $reward_coin = new RewardCoin;
        $reward_coin->reward_type = $request->reward_type;
        $reward_coin->user_id = $this->user->id;
        $reward_coin->coins =  $request->lucky_coin;
        $reward_coin->notification_status = 0;
        $reward_coin->created_at = date('Y-m-d H:i:s');
        $reward_coin->updated_at = date('Y-m-d H:i:s');
        $reward_coin->save();

        if($reward_coin->id > 0){

          $user = User::find($this->user->id);
          $getRewardCoin = RewardCoin::selectRaw('SUM(coins) as total_coin')->where('user_id',$this->user->id)->first();
          $before_coin = $user->available_coins;
          $user->available_coins = round(($user->available_coins + $reward_coin->coins));
          $user->save();
          $after_coin = $user->available_coins;
          $this->sendNoticationMilestone($before_coin,$after_coin,$this->user->id);
          return response()->json(array('message' => 'You have won '.$reward_coin->coins.' coins in your Walk & Earn wallet.','status' => 'success','user' => $user), 200);
        }
      }else{
        $starttime = strtotime(date('H:i:s',strtotime($check_user->created_at)));
        $endtime = strtotime(date('H:i:s'));
        $timediff = $endtime - $starttime;

        if( $timediff > 3600){

          $reward_coin = new RewardCoin;
          $reward_coin->reward_type = $request->reward_type;
          $reward_coin->user_id = $this->user->id;
          $reward_coin->coins =  $request->lucky_coin;
          $reward_coin->notification_status = 0;
          $reward_coin->created_at = date('Y-m-d H:i:s');
          $reward_coin->updated_at = date('Y-m-d H:i:s');
          $reward_coin->save();

          if($reward_coin->id > 0){

            $user = User::find($this->user->id);
            $getRewardCoin = RewardCoin::selectRaw('SUM(coins) as total_coin')->where('user_id',$this->user->id)->first();
            $before_coin = $user->available_coins;
            $user->available_coins = round(($user->available_coins + $reward_coin->coins));
            $user->save();
            $after_coin = $user->available_coins;
            $this->sendNoticationMilestone($before_coin,$after_coin,$this->user->id);
            return response()->json(array('message' => 'You have won '.$reward_coin->coins.' coins in your Walk & Earn wallet.','status' => 'success','user' => $user), 200);
          }
        }else{
          return response()->json(array('message' => 'You can add after one hour.','status' => 'error'), 403);
        }
      }
    }else{
      return response()->json(array('message' => 'User not found.','status' => 'error'), 404);
    }
  }
}
