<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Validator;
use App\Role;
use App\User;
use App\Step;
use App\Reward;
use App\Wallet;
use App\PaytmRequest;
//use Excel;

use App\Exports\PaytmRequestsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Http\Helper\Helper;

use App\Http\Controllers\Api\FcmNotificationController as FcmNotificationController;

class PaytmController extends Controller
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
       $paytm_requests = PaytmRequest::where('status','1')->where('hold_status','1')->orderBy('id', 'desc')->get();

       foreach ($paytm_requests as $key => $value) {
          $paytm_paid_count = PaytmRequest::where('user_id',$value->user_id)->where('status','2')->orderBy('id', 'desc')->count();
          $total_paid_by_user = PaytmRequest::selectRaw('SUM(amount) as paid_amt')->where('user_id',$value->user_id)->where('status','2')->orderBy('id', 'desc')->first();
          $duplicate_count = PaytmRequest::where('paytm_mobile_number',$value->paytm_mobile_number)->where('user_id','!=',$value->user_id)->groupBy('user_id')->orderBy('id', 'desc')->count();
          $value->paytm_paid_count = $paytm_paid_count;
          $value->paid_amt = $total_paid_by_user->paid_amt;
          $lastSeen = Step::where('user_id',$value->user_id)->orderBy('id', 'desc')->first();
          $value->last_seen = !empty($lastSeen) ? $lastSeen->created_at : '';
          $value->duplicate_count = $duplicate_count >0 ? $duplicate_count :'';
        } 

       $pending_rupees = PaytmRequest::selectRaw('SUM(amount) as pending_rupee')->where('status','1')->orderBy('id', 'desc')->first(); 
       return view('paytm_request.index')->with(['paytm_requests' => $paytm_requests,'pending_rupee' => $pending_rupees]);
    }

    public function holdRequest()
    { 
       $paytm_requests = PaytmRequest::where('status','1')->where('hold_status','2')->orderBy('id', 'desc')->get();

       foreach ($paytm_requests as $key => $value) {
          $paytm_paid_count = PaytmRequest::where('user_id',$value->user_id)->where('status','2')->orderBy('id', 'desc')->count();
          $total_paid_by_user = PaytmRequest::selectRaw('SUM(amount) as paid_amt')->where('user_id',$value->user_id)->where('status','2')->orderBy('id', 'desc')->first();
          $duplicate_count = PaytmRequest::where('paytm_mobile_number',$value->paytm_mobile_number)->where('user_id','!=',$value->user_id)->groupBy('user_id')->orderBy('id', 'desc')->count();
          $value->paytm_paid_count = $paytm_paid_count;
          $value->paid_amt = $total_paid_by_user->paid_amt;
          $lastSeen = Step::where('user_id',$value->user_id)->orderBy('id', 'desc')->first();
          $value->last_seen = !empty($lastSeen) ? $lastSeen->created_at : '';
          $value->duplicate_count = $duplicate_count >0 ? $duplicate_count :'';
        } 

       $pending_rupees = PaytmRequest::selectRaw('SUM(amount) as pending_rupee')->where('status','1')->where('hold_status','2')->orderBy('id', 'desc')->first(); 
       return view('paytm_request.hold')->with(['paytm_requests' => $paytm_requests,'pending_rupee' => $pending_rupees]);
    }

    public function show($id)
    {
      $paytm_request = PaytmRequest::find($id);
      return view('paytm_request.show')->with(['paytm_request' => $paytm_request]);
    }

    public function transactions()
    {
      $paytm_requests = PaytmRequest::where('status','!=','1')->orderBy('updated_at', 'desc')->get(); 
      return view('transaction.index')->with(['paytm_requests' => $paytm_requests]);
    }


     public function update(Request $request, $id)
    { 
      $paytm_request = PaytmRequest::find($id);
      if($request->status !=4){
        $paytm_request->status= $request->status;
        $paytm_request->hold_status= '1';
      }else{
        $paytm_request->status= '1';
        $paytm_request->hold_status= '2';
      }
      $paytm_request->remark= $request->remark;
      $paytm_request->paid_time = date('Y-m-d');
      $paytm_request->save();
      request()->session()->flash('success', 'Paytm updated successfully!');
      $country_code = '91';
      $mobile = $paytm_request->paytm_mobile_number;

      $user_info = User::find($paytm_request->user_id);

      if($request->status ==3 || $request->status ==2){

        if($request->status ==3){

        $message = urlencode("Rejected ! PayTm requested Amount of Rs.".$paytm_request->amount."/- has been rejected and same has been added to your Wallet. ".$request->remark." For any assistance contact walkearn2019@gmail.com. Order ID. WER".str_pad($paytm_request->order_id, 10, 0, STR_PAD_LEFT ).".");
        
        $message1 = "Rejected ! PayTm requested Amount of Rs.".$paytm_request->amount."/- has been rejected and same has been added to your Wallet . For any assistance contact walkearn2019@gmail.com. Order ID. WER".str_pad($paytm_request->order_id, 10, 0, STR_PAD_LEFT )."."; 

        }elseif($request->status ==2){

          $message = urlencode("Congratulations ! Requested Amount of Rs.".$paytm_request->amount."/- has been successfully credited to your PayTm No. ".$mobile.". Order ID. WER".str_pad($paytm_request->order_id, 10, 0, STR_PAD_LEFT ).". Walk more and earn more with Walk and Earn. Cheers!");
          
          $message1 = "Congratulations ! Requested Amount of Rs.".$paytm_request->amount."/- has been successfully credited to your PayTm No. ".$mobile.". Order ID. WER".str_pad($paytm_request->order_id, 10, 0, STR_PAD_LEFT ).". Walk more and earn more with Walk and Earn. Cheers!"; 
        }

        $notification_data = ['description' => $message1, 'title' => 'Walk & Earn','type' => 'Wallet'];
        Helper::sendSms($country_code, $mobile, $message);

        //push notification
        $action = "in.hexalab.walkandearn.activity.WalletActivity";
        if($user_info->device_type=='1'){

          $android_token = User::where('id',$paytm_request->user_id)->where('device_type', '1')->get()->pluck('fcm_token')->toArray();
          FcmNotificationController::sendNotificationForPaytm($android_token, $notification_data, $action, '1');
       
        }elseif ($user_info->device_type=='2') {
          
          $ios_token = User::where('id',$paytm_request->user_id)->where('device_type', '2')->get()->pluck('fcm_token')->toArray();
          FcmNotificationController::sendNotificationForPaytm($ios_token, $notification_data, $action, '2');
        }
        
        //$action = "in.hexalab.walkandearn.activity.HomeActivity";
        //FcmNotificationController::sendNotificationForBroadcast($ios_token, $notification_data, $action, '2');
        //FcmNotificationController::sendNotificationForBroadcast($android_token, $notification_data, $action, '1');
        //FcmNotificationController::sendNotification($android_token, $notification_data, $action);
        //FcmNotificationController::sendNotification($ios_token, $notification_data, $action);
        
      }

      //return redirect()->back();
       return redirect()->to('/paytm_requests');
    }

    public function download()
    {
        //$paytm_requests = PaytmRequest::where('status','!=','1')->orderBy('id', 'desc')->get(); 
      //$paytm_requests = PaytmRequest::all();
        Excel::create('paytm_request', function($excel) use($paytm_requests){
          $excel->sheet('paytm_request sheet', function($sheet) use($paytm_requests){
            $sheet->setOrientation('landscape');
            //$sheet->fromArray($inspections);
            $sheet->loadView('transaction.excel', ['paytm_requests' => $paytm_requests]);
          });
        })->export('xls');
    }

    //  public function export() 
    // {
    //     return Excel::download(new PaytmRequestsExport, 'users.xlsx');
    // }

    public function getPerDayAmount(){

      $paytm_requests1 = PaytmRequest::selectRaw('*,paid_time')->where('status','2')->groupBy('paid_time')->orderBy('id', 'desc')->get();

      if(!empty($paytm_requests)){
        $paytm_requests = $paytm_requests1;
      }else{
        $paytm_requests = PaytmRequest::selectRaw('*,DATE(updated_at) as update_date')->where('status','2')->groupBy('update_date')->orderBy('id', 'desc')->get();
      }

      foreach ($paytm_requests as $key => &$value) {
       $amountArr = PaytmRequest::selectRaw('SUM(amount) as paid_per_day_amount')->where('status','2')->whereDate('updated_at','=',$value->update_date)->first();
       $value->paid_per_day_amount = $amountArr->paid_per_day_amount;
      }

     return view('transaction.pay')->with(['paytm_requests' => $paytm_requests]);
    }

    public function getPerDayPendingAmount(){
      $paytm_requests = PaytmRequest::selectRaw('*,DATE(created_at) as create_date')->where('status','1')->groupBy('create_date')->orderBy('id', 'desc')->get();

      foreach ($paytm_requests as $key => &$value) {
       $amountArr = PaytmRequest::selectRaw('SUM(amount) as pending_per_day_amount')->where('status','1')->whereDate('created_at','=',$value->create_date)->first();
       $value->pending_per_day_amount = $amountArr->pending_per_day_amount;
      }

     return view('transaction.pending')->with(['paytm_requests' => $paytm_requests]);
    }

  // public function updateImage(){
  //   $url= 'http://ec2-13-235-153-119.ap-south-1.compute.amazonaws.com/storage';
  //   $paytm_requests_data = PaytmRequest::orderBy('id', 'desc')->get();

  //   foreach ($paytm_requests_data as $key => $value) {
  //       $arr = explode('storage', $value->qr_code);
  //       $update_QR = PaytmRequest::find($value->id);
  //       $update_QR->qr_code = $url.$arr[1];
  //       $update_QR->save();
  //       //print_r($arr);
  //   }  
  // }
}
