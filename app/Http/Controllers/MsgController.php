<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Validator;
use App\Role;
use App\User;
use App\Message;

use App\Http\Helper\Helper;

use App\Http\Controllers\Api\FcmNotificationController as FcmNotificationController;

class MsgController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
          $this->user = Auth::user();
          return $next($request);
        });

        $this->admin_role = Role::where('role', 'Admin')->first();
    }
    
     public function sendMsgByUser(Request $request)
    { 
      $user = User::find($request->user_id); 
      
      //if($user->phone_number !='' && $user->fcm_token){
      if($user->fcm_token){
        $messages_data = new Message;
        $messages_data->user_id= $request->user_id;
        $messages_data->message= $request->message;
        $messages_data->broadcast_message= $request->broadcast_message;
        $messages_data->save();
        request()->session()->flash('success', 'Message updated successfully!');
        $country_code = '91';

        $mobile = $user->phone_number;

        $message = urlencode($request->message);
        $message1 = $request->broadcast_message;

        $notification_data = ['description' => $message1, 'title' => 'Walk & Earn','type' => 'message'];
        //Helper::sendSms($country_code, $mobile, $message);

        //push notification
        //$ios_token = User::where('device_type', '2')->get()->pluck('fcm_token')->toArray();
        //$android_token = User::where('id',$request->user_id)->where('device_type', '1')->get()->pluck('fcm_token')->toArray();
        $action = "in.hexalab.walkandearn.activity.HomeActivity";
        //FcmNotificationController::sendNotification($android_token, $notification_data, $action);
        if($user->device_type=='1'){

          $android_token = User::where('id',$request->user_id)->where('device_type', '1')->get()->pluck('fcm_token')->toArray();
          FcmNotificationController::sendNotificationForPaytm($android_token, $notification_data, $action, '1');
       
        }elseif ($user->device_type=='2') {
          
          $ios_token = User::where('id',$request->user_id)->where('device_type', '2')->get()->pluck('fcm_token')->toArray();
          FcmNotificationController::sendNotificationForPaytm($ios_token, $notification_data, $action, '2');
        }
        
      }else{
        request()->session()->flash('error', 'Mobile number is required.');
      }
      return redirect()->to('/users/'.$request->user_id);
      //FcmNotificationController::sendNotificationForBroadcast($ios_token, $notification_data, $action, '2');
      //FcmNotificationController::sendNotificationForBroadcast($android_token, $notification_data, $action, '1');
      //return redirect()->back();
       
    }
}
