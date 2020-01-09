<?php
  
namespace App\Http\Controllers;
  
use Illuminate\Http\Request;
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
  
class AjaxController extends Controller
{
	public function __construct()
	{
	$this->middleware(function($request, $next){
	$this->user = Auth::user();
	return $next($request);
	});

	$this->admin_role = Role::where('role', 'Admin')->first();
	}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxPagination(Request $request)
    {
        

        if($request->search != ''){
        	$search = $request->search;
        	$data = User::where('role_id', '!=', $this->admin_role->id)->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")->orderBy('id', 'desc')->paginate(50); 
        //print_r($data); exit; 

        }else{
        	$data = User::where('role_id', '!=', $this->admin_role->id)->orderBy('id', 'desc')->paginate(50);
        }

	    foreach ($data as $key => $value) {
	      
	      $wallet_info = Wallet::selectRaw('SUM(rupees) as total_wallet_bal')->where('user_id',$value->id)->where('scratch_status','2')->first();
	      $paytm_info = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$value->id)->where('status','!=',3)->first();
	      $reddem_paytm = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('user_id',$value->id)->where('status',2)->first();
	      $wallet_balance = round($wallet_info->total_wallet_bal - $paytm_info->paytm_bal);
	      $value->wallet_balance = $wallet_balance;
	      $value->paytm_paid_amt = $reddem_paytm->paytm_bal;
	      $value->refferal_count = RewardCoin::where('user_id',$value->id)->where('reward_type','1')->count();
	      $value->reward_ads = RewardCoin::where('user_id',$value->id)->where('reward_type','2')->count();

	      //$lastSeen = Step::where('user_id',$value->id)->orderBy('id', 'desc')->first();
	      $lastSeen = $value->updated_at;
	      //$value->last_seen = ($lastSeen->created_at != NULL) ? $lastSeen->created_at : NULL;
	      $value->last_seen = !empty($lastSeen) ? $lastSeen : '';
	     } 


  
        if ($request->ajax()) {
            return view('ajax.presult', compact('data'));
        }
  
        return view('ajax.ajaxPagination',compact('data'));
    }
}
