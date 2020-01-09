<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Validator;
use App\Setting;
use App\SettingLog;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
         // $this->user = Auth::user();
          return $next($request);
        });

       // $this->admin_role = Role::where('role', 'Admin')->first();
    }

    public function edit($id){
        $setting = Setting::find($id);
        $settingLog = SettingLog::orderBy('id','desc')->get();
        return view('setting.edit')->with('setting', $setting)->with('settingLog',$settingLog);
    }

    public function update(Request $request, $id)
    {
      $validate = Validator::make($request->all(), [
        'reward_ads_coin' => 'required',
        'frd_refferal_coin' => 'required',
        'weekly_redem_coin' => 'required',
        'reward_ads_time_interval' => 'required'
      ])->validate();
      

      $setting = Setting::find($id);
      $setting->reward_ads_coin = $request->reward_ads_coin;
      $setting->frd_refferal_coin = $request->frd_refferal_coin;
      $setting->weekly_redem_coin = $request->weekly_redem_coin;
      $setting->reward_ads_time_interval = $request->reward_ads_time_interval;
      $setting->save();

      $settingLog = new SettingLog;
      $settingLog->reward_ads_coin = $setting->reward_ads_coin;
      $settingLog->frd_refferal_coin = $setting->frd_refferal_coin;
      $settingLog->weekly_redem_coin = $setting->weekly_redem_coin;
      $settingLog->reward_ads_time_interval = $setting->reward_ads_time_interval;
      $settingLog->bucket_amt = $setting->bucket_amt;
      $settingLog->save();
      request()->session()->flash('success', 'Setting updated successfully!');

      return redirect()->back();
    }

    public function bucketUpdate(Request $request, $id){
      $validate = Validator::make($request->all(), [
        'bucket_amt' => 'required',
      ])->validate();
      

      $setting = Setting::find($id);
      $setting->bucket_amt = $request->bucket_amt;
      $setting->save();

      $settingLog = new SettingLog;
      $settingLog->reward_ads_coin = $setting->reward_ads_coin;
      $settingLog->frd_refferal_coin = $setting->frd_refferal_coin;
      $settingLog->weekly_redem_coin = $setting->weekly_redem_coin;
      $settingLog->reward_ads_time_interval = $setting->reward_ads_time_interval;
      $settingLog->bucket_amt = $setting->bucket_amt;
      $settingLog->save();
      request()->session()->flash('success', 'Prediction Bucket updated successfully!');

      return redirect()->back();
    }
}
