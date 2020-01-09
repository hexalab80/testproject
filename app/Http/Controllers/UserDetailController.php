<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Mail;

use Auth;
use Validator;
use App\User;
use App\Role;
use App\Wallet;
use App\PaytmRequest;
use App\RewardCoin;
use Hash;
use App\Setting;
use App\Step;
use App\Message;
use App\Mail\MessageEmail;

use App\Http\Controllers\Api\FcmNotificationController as FcmNotificationController;

class UserDetailController extends Controller
{
  public function __construct()
  {
      $this->middleware(function($request, $next){
        $this->user = Auth::user();
        return $next($request);
      });

      $this->admin_role = Role::where('role', 'Admin')->first();
  }
  
  public function index()
  { 
     $users = User::where('role_id', '!=', $this->admin_role->id)->orderBy('id', 'desc')->get(); //echo '<pre>'; print_r($users); exit;
     foreach ($users as $key => $value) {
      
      $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$value->id)->where('scratch_status','2')->first();
      $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$value->id)->where('status','!=',3)->first();
      $reddem_paytm = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$value->id)->where('status',2)->first();
      $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
      $value->wallet_balance = $wallet_balance;
      $value->paytm_paid_amt = $reddem_paytm->paytm_bal;
      $value->refferal_count = RewardCoin::where('user_id',$value->id)->where('reward_type','1')->count();
      $value->reward_ads = RewardCoin::where('user_id',$value->id)->where('reward_type','2')->count();

      $lastSeen = Step::where('user_id',$value->id)->orderBy('id', 'desc')->first();
      //$value->last_seen = ($lastSeen->created_at != NULL) ? $lastSeen->created_at : NULL;
      $value->last_seen = !empty($lastSeen) ? $lastSeen->created_at : '';
     } 
     return view('user.index')->with(['users' => $users]);
  }


  public function user_list(){

    $users = User::where('role_id', '!=', $this->admin_role->id)->orderBy('id', 'desc')->paginate(1);

    return view('user.index', ['users' => $users]);
  }

  public function serverProcessing(Request $request)
  {
    $columns = array(
        0 => "serial_number",
        1 => "id",
        2 => "name",
        3 => "role",
        4 => "email",
        5 => "phone_number",
        6 => "available_coins",
        7 => "total_steps",
        8 => "refferal_count",
        9 => "reward_ads",
        10 => "wallet_balance",
        11 => "paytm_paid_amt",
        12 => "email_verified",
        13 => "created_at",
        14 => "join_time",
        15 => "last_seen",
        16 => "device_type",
        17 => "user_app_version",
        18 => "action"
      );

       $totalData = User::where('role_id', '!=', $this->admin_role->id)->orderBy('id', 'desc')->count();
       $limit = $request->length;
       $start = $request->start;
       $order = $columns[$request->input('order.0.column')];
       $dir = "desc";

       if(empty($request->input('search.value'))){
        
        $users = User::where('role_id', '!=', $this->admin_role->id)->offset($start)
                            ->limit($limit)
                            ->orderBy("id", $dir)
                            ->get();

      foreach ($users as $key => $value) {

        $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$value->id)->where('scratch_status','2')->first();
        $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$value->id)->where('status','!=',3)->first();
        $reddem_paytm = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$value->id)->where('status',2)->first();
        $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
        $value->wallet_balance = $wallet_balance;
        $value->paytm_paid_amt = $reddem_paytm->paytm_bal;
        $value->refferal_count = RewardCoin::where('user_id',$value->id)->where('reward_type','1')->count();
        $value->reward_ads = RewardCoin::where('user_id',$value->id)->where('reward_type','2')->count();

        $lastSeen = Step::where('user_id',$value->id)->orderBy('id', 'desc')->first();
        $value->last_seen = !empty($lastSeen) ? $lastSeen->created_at : '';
      }                     
        $totalFiltered = User::where('role_id', '!=', $this->admin_role->id)->orderBy('id', 'desc')->count();
      }else{
          $search = $request->input('search.value');
          $users = User::where('role_id', '!=', $this->admin_role->id)->where('name', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%")
                            ->orWhere('available_coins', 'like', "%{$search}%")
                            ->orWhere('total_steps', 'like', "%{$search}%")
                            ->orWhere('user_app_version', 'like', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy("id", $dir)
                            ->get();

          foreach ($users as $key => $value) {
            
            $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$value->id)->where('scratch_status','2')->first();
            $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$value->id)->where('status','!=',3)->first();
            $reddem_paytm = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$value->id)->where('status',2)->first();
            $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
            $value->wallet_balance = $wallet_balance;
            $value->paytm_paid_amt = $reddem_paytm->paytm_bal;
            $value->refferal_count = RewardCoin::where('user_id',$value->id)->where('reward_type','1')->count();
            $value->reward_ads = RewardCoin::where('user_id',$value->id)->where('reward_type','2')->count();

            $lastSeen = Step::where('user_id',$value->id)->orderBy('id', 'desc')->first();
            $value->last_seen = !empty($lastSeen) ? $lastSeen->created_at : '';
          }

          $totalFiltered = User::where('role_id', '!=', $this->admin_role->id)->where('name', 'like', "%{$search}%")
                            ->orWhere('id', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('phone_number', 'like', "%{$search}%")
                            ->orWhere('available_coins', 'like', "%{$search}%")
                            ->orWhere('total_steps', 'like', "%{$search}%")
                            ->orWhere('user_app_version', 'like', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy("id", $dir)
                            ->count();
      }

      $data = array();

      if($users){
        foreach ($users as $key => $user) {
          $id = $user->id;
          $user_url = "/users/".$user->id;
          $block_url = "'/users/$user->id'";
          $temp_status = $user->status=='2' ? 'Unblock' : 'Block';
          $temp_status_pop = $user->status!=2 ? 2: 1;

          $style = $user->status =='2' ? 'background-color: #ff0000;' : '';

          $nestedData['serial_number'] = $start + ++$key;

          $nestedData['id'] = '<p style="'.$style.'">'.$user->id.'</p>';
          $nestedData['name'] = '<a href="'.$user_url.'">'.ucwords($user->name).'</a>';
          $nestedData['role'] = $user->role->role;
          $nestedData['email'] = $user->email;
          $nestedData['phone_number'] = $user->phone_number;
          $nestedData['available_coins'] = $user->available_coins;
          $nestedData['total_steps'] = $user->total_steps;
          $nestedData['refferal_count'] = $user->refferal_count;
          $nestedData['reward_ads'] = $user->reward_ads;
          $nestedData['wallet_balance'] = $user->wallet_balance;
          $nestedData['paytm_paid_amt'] = $user->paytm_paid_amt;
          if($user->email_verified_at){
            $nestedData['email_verified'] = '<i class="material-icons green-text">check</i>';
          }else{
            $nestedData['email_verified'] =  '<i class="material-icons red-text">clear</i>';
          }
          $temp_created_at = $user->created_at->format('Y-m-d H:i:s');
          $nestedData['created_at'] = $temp_created_at;
          $nestedData['join_time'] = $user->created_at->diffForHumans();
          $nestedData['device_type'] = $user->device_type == '1' ? 'Android' :'IOS';
          $nestedData['user_app_version'] = $user->user_app_version;
          $nestedData['last_seen'] = $user->last_seen != '' ? \Carbon\Carbon::createFromTimeStamp(strtotime($user->last_seen))->diffForHumans() : '';

          

          $nestedData['action'] = '<a href="'.$user_url.'" class="waves-effect waves-light btn"> <i class="material-icons">remove_red_eye</i> </a><br><br>
              <a href="javascript:void(0);" class="waves-effect waves-light btn" alt="'.$temp_status.'" onclick="userPopup('.$id.', '.$block_url.','.$temp_status_pop.')">'.$temp_status.'</a>';

          $data[] = $nestedData;
        }
      }

      $json_data = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($totalData),
        "recordsFiltered" => intval($totalFiltered),
        "data" => $data
      );

      return response()->json($json_data);
  }

  public function show($id)
  {
    $user = User::find($id);
    if($user){
      $paytm_requests = PaytmRequest::where('user_id',$id)->orderBy('id', 'desc')->get(); 
      $wallets = Wallet::where('user_id',$id)->orderBy('id', 'desc')->get();
      $reward_coins = RewardCoin::where('user_id',$id)->get();
      $messages_data = Message::where('user_id',$id)->get();
      $lastSeen = Step::select('created_at')->where('user_id',$id)->orderBy('id', 'desc')->first();
      $user->last_seen = !empty($lastSeen) ? $lastSeen->created_at : '';
      
      $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$id)->where('scratch_status','2')->first();
      $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$id)->where('status','!=',3)->first();
      $reddem_paytm = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$id)->where('status',2)->first();
      $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
      $user->wallet_balance = $wallet_balance;
      $user->paytm_paid_amt = $reddem_paytm->paytm_bal;

      return view('user.show')->with(['user' => $user,'paytm_requests'=> $paytm_requests,'wallet_log'=> $wallets,'reward_coins' => $reward_coins,'messages_data' => $messages_data]);
    }else{
      request()->session()->flash('error', 'User not found.');
      return redirect('users');
    }
    
  }

  public function verifyEmail($user_id)
  {
      try {
        $user_id = decrypt($user_id);
      } catch (DecryptException $e) {

      }
      $user = User::find($user_id);
      if($user){
        if(is_null($user->email_verified_at)){
          $user->email_verified_at = date('Y-m-d H:i:s');
          $user->status='1';
          $user->save();

          //referal code
          /*  $check_valid_user = User::where('referal_code',$user->frd_referral_code)->first();
            $check_imei_user = User::where('imei_number',$user->imei_number)->count();

            if(!empty($check_valid_user) && ($check_imei_user==1)){
            
             // $getUser = $this->frd_referral_code_fun($check_valid_user->id);
             // $getUser1 = $this->frd_referral_code_fun($user->id);
              $setting = Setting::find(1);

              $message1 ='Congratulations! You have earned '.$setting->frd_refferal_coin.' Sweatcoins using our referral program. Refer more & earn more!';
              $notification_data = ['description' => $message1, 'title' => 'Walk & Earn', 'type' => 'Reward'];

              $android_token = User::where('id',$getUser->id)->where('device_type', '1')->get()->pluck('fcm_token')->toArray();
              //$android_token1 = User::where('id',$getUser1->id)->where('device_type', '1')->get()->pluck('fcm_token')->toArray();
              //$action = "in.hexalab.walkandearn.activity.HomeActivity";
              $action = "in.hexalab.walkandearn.activity.RewardActivity";
              FcmNotificationController::sendNotification($android_token, $notification_data, $action);
              //FcmNotificationController::sendNotification($android_token1, $notification_data, $action);
            } */
          return "Email is verified successfully.Thanks for signUp with WalknEarn.";
        }
        return "This email is already verified.";
      }
      return "User not found.";
  }

  public function change_password(){
    return view('change_password');
  }

  public function update_pass(Request $request){ 

    $validate = Validator::make($request->all(), [
                'old_password'     => 'required',
                'new_password'     => 'required|min:6',
                'confirm_password' => 'required|same:new_password',
                ])->validate(); 

    $user = User::find(Auth::user()->id);

    if(!Hash::check($request->old_password, $user->password)){
      return back()->with('error','The specified password does not match the database password');
    }else{
        $user->update([
              'password' => Hash::make($request->new_password)
          ]); 
          back()->with('success','Admin password has been changed successfully.');
          return redirect('change_password');
    }  
  }

  public function update(Request $request,$id){
    $user = User::find($id);
    $user->status = $request->status; 
    $user->save();
    if($request->status==2){
      $msg = 'blocked';
    }else{
      $msg = 'unblocked';
    }
     back()->with('success','User has been '.$msg.' successfully.');
     return redirect('users');
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
      //$reward_coin->coins = $setting->frd_refferal_coin;
      $reward_coin->reward_type = '1';
      $reward_coin->user_id = $user_id;
      $reward_coin->save();

      if($reward_coin->id > 0){

        $user = User::find($user_id);
        $getRewardCoin = RewardCoin::selectRaw('SUM(coins) as total_coin')->where('user_id',$user_id)->first();
        //return response()->json(array('message' => $getRewardCoin,'status' => 'success'), 200);
        //$user->available_coins = ($user->available_coins + $getRewardCoin->total_coin);
        //$user->available_coins = ($user->available_coins + $setting->frd_refferal_coin);
        $user->available_coins = ($user->available_coins + $reward_coin->coins);
        $user->save();
        return $user;
      }
    }
  } 

  public function lucky_coupon_cron(){


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
                $arr[] =  $check_timestamp->user_id;
            
            $check_timestamp->notification_status = 1;
            $check_timestamp->save();
              }else if(isset($check_timestamp) && ($timediff > 10800) && ($check_timestamp->notification_status=='1')){
                    $arr[] =  $check_timestamp->user_id;
                    
                    $check_timestamp->notification_status = 2;
                    $check_timestamp->save();

              }else if(isset($check_timestamp) && ($timediff > 25200) && ($check_timestamp->notification_status=='2')){
           
                    $arr[] =  $check_timestamp->user_id;
                    
                    $check_timestamp->notification_status = 0;
                    $check_timestamp->save();
              }
          
          }
          
          if($getUser->device_type=='2'){
           // echo '<pre>'; print_r($check_timestamp->user_id);
            $start_time = strtotime(date('H:i:00',strtotime($check_timestamp->created_at))); echo '<br>';
            echo $timediff = strtotime(date('H:i:00')) - $start_time;

              if(isset($check_timestamp) && ($timediff > 3600) && ($check_timestamp->notification_status=='0')){
                $arrIOS[] =  $check_timestamp->user_id;
            
                $check_timestamp->notification_status = 1;
                $check_timestamp->save();
              }else if(isset($check_timestamp) && ($timediff > 10800) && ($check_timestamp->notification_status=='1')){
                    $arrIOS[] =  $check_timestamp->user_id;
                    
                    $check_timestamp->notification_status = 2;
                    $check_timestamp->save();

              }else if(isset($check_timestamp) && ($timediff > 25200) && ($check_timestamp->notification_status=='2')){
           
                    $arrIOS[] =  $check_timestamp->user_id;
                    
                    $check_timestamp->notification_status = 0;
                    $check_timestamp->save();
              }
          }  
        }
       // echo '<pre>'; print_r($arrIOS);
       // echo '<pre>'; print_r($arr);
        if(count($arr) >0){
            //SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
            $message1 = 'Congratulation! You won a new lucky coupon. Claim & get your reward now. Limited period offer. Expires soon!';
            $notification_data = ['description' => $message1, 'title' => 'Claim reward now!', 'type' => 'broadcast'];

            $ios_token = User::whereIn('id',$arrIOS)->where('device_type', '2')->where('role_id','!=','1')->get()->pluck('fcm_token')->toArray();
            $android_token = User::whereIn('id',$arr)->where('device_type', '1')->where('role_id','!=','1')->get()->pluck('fcm_token')->toArray();
           // echo '<pre>'; print_r($android_token);
           // echo '<pre>'; print_r($ios_token);
            $action = "in.hexalab.walkandearn.activity.HomeActivity";
            //FcmNotificationController::sendNotification($android_token, $notification_data, $action);
           // FcmNotificationController::sendNotificationForPaytm($android_token, $notification_data, $action, '1');
           // FcmNotificationController::sendNotificationForPaytm($ios_token, $notification_data, $action, '2');
        } 

    /*$date = date('Y-m-d');
    $getAllUser = RewardCoin::where('reward_type','3')->where('notification_status',0)->whereDate('created_at','=',$date)->groupBy('user_id')->get();

   // echo '<pre>'; print_r($getAllUser); echo '</pre>';
    $arr = array();
    foreach ($getAllUser as $key => $value) {

      $check_timestamp = RewardCoin::where('user_id',$value->user_id)->where('reward_type','3')->whereDate('created_at','=',$date)->orderBy('id','desc')->first();
      
      $start_time = strtotime(date('H:i:00',strtotime($check_timestamp->created_at)));
      $timediff = strtotime(date('H:i:00')) - $start_time;
      
      
      if(isset($check_timestamp) && ($timediff > 3600)){
        $arr[] =  $check_timestamp->user_id;
       // $check_timestamp->notification_status = 1;
       // $check_timestamp->save();
      }
    }
    echo '<pre>'; print_r($arr);
    if(count($arr) >0){

       $message1 = 'You got a Lucky Coupon. Claim now!';
       $notification_data = ['description' => $message1, 'title' => 'Lucky Coupon', 'type' => 'broadcast'];

      $ios_token = User::whereIn('id',$arr)->where('device_type', '2')->where('role_id','!=','1')->get()->pluck('fcm_token')->toArray();
      $android_token = User::whereIn('id',$arr)->where('device_type', '1')->where('role_id','!=','1')->get()->pluck('fcm_token')->toArray();
      echo '<pre>'; print_r($android_token);
      $action = "in.hexalab.walkandearn.activity.HomeActivity";
      //FcmNotificationController::sendNotification($android_token, $notification_data, $action);
    } */
  }

  public function sendMail(){
    $out = array();
    $users = User::where('device_type','1')->where('role_id','2')->where('user_app_version','1.0.3')->orderBy('id','asc')->limit(100)->get();
    // \App\User::chunk(100,function($users) use(&$out){
    //   foreach($users as $user){
    //    // if($user->device_type == '1' && $user->device_type == '2'){
    //     $out[] = $user->email;
    //     //}
    //   }
     // echo '<pre>'; print_r($out);  echo '</pre>';
      
    //});
    //$user_info = User::find(1646);
   // Mail::to($user_info)->send(new MessageEmail($user_info));
    foreach ($users as $key => $value) {
       echo $value->email;
        echo '<br>';
        Mail::to($value)->send(new MessageEmail($value));
      }
      //last user id 2311
  }


}
