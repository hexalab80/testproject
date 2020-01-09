<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Auth;
use Validator;
use App\User;
use App\Role;
use App\Setting;
use App\Prediction;
use App\TempPrediction;
use App\Mail\PredictionOtpEmail;

class PredictionController extends Controller
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
		$time_date_data = date('Y-m-d H:i:s');
		$meeting_time = date('Y-m-d H:i:s', strtotime($time_date_data) - 60 * 60 * 12); 
		$users = User::where('role_id', '!=', $this->admin_role->id)->where('available_coins','>',200)->where('updated_at', '>=', $meeting_time)->orderBy('id', 'desc')->count();
		$setting = Setting::find(1);
		$user_cutoff = 0 ;
		if($users > 9){
			$user_cutoff = floor(($users * 20)/100);
		}

		$prediction = Prediction::whereDate('date',date('Y-m-d'))->first();
		$predictions = Prediction::all();

		return view('prediction.index')->with(['user_count' => $users,'setting' => $setting, 'user_cutoff' => $user_cutoff,'prediction' => $prediction,'predictions' => $predictions]);
	}

	public function store(Request $request){

		$validate = Validator::make($request->all(), [
        'otp' => 'required',
        'bucket_amt' => 'required',
        'user_cutoff' => 'required'
        ])->validate();

        $check_otp = Prediction::where('otp',$request->otp)->first();
        if(empty($check_otp)){
        	request()->session()->flash('error', 'Otp is invalid.');
        	return redirect()->back();
        }else{
        	$check_otp->otp = NULL;
        	$check_otp->save();
        }

		$from= date('Y-m-d',strtotime('-15 days'));
		$to = date('Y-m-d');
		$check_user_ids = TempPrediction::whereBetween('date',[$from, $to])->pluck('user_id')->toArray();
		$time_date_data = date('Y-m-d H:i:s');
		$meeting_time = date('Y-m-d H:i:s', strtotime($time_date_data) - 60 * 60 * 12);
		$rupees = $request->bucket_amt/$request->user_cutoff;
		$setting = Setting::find(1);
		$setting->bucket_amt = $request->bucket_amt;
		$setting->save();

		if(empty($check_user_ids)){
			$users = User::where('role_id', '!=', $this->admin_role->id)->where('available_coins','>',200)->where('updated_at', '>=', $meeting_time)->orderByRaw("RAND()")->take($request->user_cutoff)->pluck('id')->toArray();

		}else{

			$users = User::where('role_id', '!=', $this->admin_role->id)->where('available_coins','>',200)->where('updated_at', '>=', $meeting_time)->whereNotIn('id',$check_user_ids)->orderByRaw("RAND()")->take($request->user_cutoff)->pluck('id')->toArray();
		}

		foreach ($users as $key => $value) {
			$temp_prediction = new TempPrediction;
			$temp_prediction->user_id = $value;
			$temp_prediction->date = date('Y-m-d');
			$temp_prediction->rupee = $rupees;
			$temp_prediction->status = 0;
			$temp_prediction->save();
		}

		// $prediction = new Prediction;
		// $prediction->user_count = $request->user_count;
		// $prediction->user_cutoff = $request->user_cutoff;
		// $prediction->bucket_amt = $request->bucket_amt;
		// $prediction->date = date('Y-m-d');
		// $prediction->save();

		request()->session()->flash('success', 'Prediction users added successfully!');
        return redirect()->back();
	}

	public function sentotp(Request $request){


		$prediction = new Prediction;
		$prediction->user_count = $request->user_count;
		$prediction->user_cutoff = $request->user_cutoff;
		$prediction->bucket_amt = $request->bucket_amt;
		$prediction->otp = rand(1000,9999);
		$prediction->date = date('Y-m-d');
		$prediction->save();

		
		$email = "manishg440@gmail.com";
		$name = "Manish";
		Mail::to($email)->send(new PredictionOtpEmail($name,$prediction->otp));
		request()->session()->flash('success', 'Prediction added successfully!');
        return redirect()->back();
	}
}
