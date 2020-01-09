<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\RewardCoin;
use App\User;
use DB;
use App\Http\Controllers\Api\FcmNotificationController as FcmNotificationController;

class FiveMintuesUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fivemintues:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lucky coupon sent Push Notification.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $date = date('Y-m-d');
        $getAllUser = RewardCoin::where('reward_type','3')->whereDate('created_at','=',$date)->groupBy('user_id')->get();

        //$getAllUser = RewardCoin::whereIn('user_id',[161,124,523,296])->where('reward_type','3')->whereDate('created_at','=',$date)->groupBy('user_id')->get();

       // echo '<pre>'; print_r($getAllUser); echo '</pre>';
        $arr = array(); $arrIOS=array();
        foreach ($getAllUser as $key => $value) {

          $check_timestamp = RewardCoin::where('user_id',$value->user_id)->where('reward_type','3')->whereDate('created_at','=',$date)->orderBy('id','desc')->first();
          $getUser = User::find($check_timestamp->user_id);
          
          if( $getUser->device_type=='1'){

            $start_time = strtotime(date('H:i:00',strtotime($check_timestamp->created_at)));
            $timediff = strtotime(date('H:i:00')) - $start_time;

            if(isset($check_timestamp) && ($timediff > 3600) && ($check_timestamp->notification_status=='0')){
                // 1 hour 
                $arr[] =  $check_timestamp->user_id;
                $check_timestamp->notification_status = 1;
                $check_timestamp->save();
              }else if(isset($check_timestamp) && ($timediff > 10800) && ($check_timestamp->notification_status=='1')){
                // 3 hours    
                $arr[] =  $check_timestamp->user_id;
                $check_timestamp->notification_status = 2;
                $check_timestamp->save();

              }else if(isset($check_timestamp) && ($timediff > 25200) && ($check_timestamp->notification_status=='2')){
                // 7 hours
                $arr[] =  $check_timestamp->user_id;
                $check_timestamp->notification_status = 3;
                $check_timestamp->save();
              }else if(isset($check_timestamp) && ($timediff > 50400) && ($check_timestamp->notification_status=='3')){
                
                //14 hours
                $arr[] =  $check_timestamp->user_id;
                $check_timestamp->notification_status = 4;
                $check_timestamp->save();
              }
          
          }
          
          if($getUser->device_type=='2'){

           // echo '<pre>'; print_r($check_timestamp->user_id);
            $start_time = strtotime(date('H:i:00',strtotime($check_timestamp->created_at))); ///echo '<br>';
            $timediff = strtotime(date('H:i:00')) - $start_time;

              if(isset($check_timestamp) && ($timediff > 3600) && ($check_timestamp->notification_status=='0')){
                // 1 hour
                $arrIOS[] =  $check_timestamp->user_id;
                $check_timestamp->notification_status = 1;
                $check_timestamp->save();
              }else if(isset($check_timestamp) && ($timediff > 10800) && ($check_timestamp->notification_status=='1')){
                //3 hours  
                $arrIOS[] =  $check_timestamp->user_id;
                $check_timestamp->notification_status = 2;
                $check_timestamp->save();

              }else if(isset($check_timestamp) && ($timediff > 25200) && ($check_timestamp->notification_status=='2')){
                //7 hours  
                $arrIOS[] =  $check_timestamp->user_id;
                $check_timestamp->notification_status = 3;
                $check_timestamp->save();
              }else if(isset($check_timestamp) && ($timediff > 50400) && ($check_timestamp->notification_status=='3')){
                // 14 hours
                $arrIOS[] =  $check_timestamp->user_id;
                $check_timestamp->notification_status = 4;
                $check_timestamp->save();
              }
          }  
        }
        
       // echo '<pre>'; print_r($arr);
        if(count($arr) >0){
            //SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
            $message1 = 'Congratulation! You won a new lucky coupon. Claim & get your reward now. Limited period offer. Expires soon!';
            $notification_data = ['description' => $message1, 'title' => 'Claim reward now!', 'type' => 'broadcast'];

            $ios_token = User::whereIn('id',$arrIOS)->where('device_type', '2')->where('role_id','!=','1')->get()->pluck('fcm_token')->toArray();
            $android_token = User::whereIn('id',$arr)->where('device_type', '1')->where('role_id','!=','1')->get()->pluck('fcm_token')->toArray();
            //echo '<pre>'; print_r($android_token);
            $action = "in.hexalab.walkandearn.activity.HomeActivity";
            FcmNotificationController::sendNotification($android_token, $notification_data, $action);
           // FcmNotificationController::sendNotificationForPaytm($android_token, $notification_data, $action, '1');
            FcmNotificationController::sendNotificationForPaytm($ios_token, $notification_data, $action, '2');
        } 

       // DB::table('test')->insert(['name' => 'Testing']);
    }
}
