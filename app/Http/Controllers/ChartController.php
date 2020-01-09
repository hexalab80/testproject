<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Charts;
use App\User;
use App\PaytmRequest;
use App\Reward;
use App\Wallet;
use DB;

class ChartController extends Controller
{
    //
    public function index()
    {
      $users = User::where(DB::raw("(DATE_FORMAT(created_at,'%Y'))"),date('Y'))->get();
      // $users =User::where()->get();
        $chart = Charts::database($users, 'bar', 'highcharts')
            ->title("Monthly new Register Users")
            ->elementLabel("Total Users")
            ->dimensions(900, 350)
            ->responsive(false)
            ->groupByMonth(date('Y'), true);

            $total_android_count = User::where('role_id','2')->where('device_type','1')->count();

            $user_versions = User::where('role_id','2')->where('device_type','1')->groupBy('user_app_version')->get();
            foreach ($user_versions as $key => $user_ver) {
                $count = User::where('role_id','2')->where('device_type','1')->where('user_app_version',$user_ver->user_app_version)->count();
                $userCountArr[] = $count;
                $userVersionArr[] = $user_ver->user_app_version;
            }

            $pie  =  Charts::create('pie', 'highcharts')
            ->title('Android User App Version Report ('.$total_android_count.')')
            ->labels($userVersionArr)
            ->values($userCountArr)
            ->dimensions(900,350)
            ->responsive(false);

            $total_ios_count = User::where('role_id','2')->where('device_type','2')->count();

            $user_version_ios = User::where('role_id','2')->where('device_type','2')->groupBy('user_app_version')->get();
            foreach ($user_version_ios as $key => $user_ver) {
                $count = User::where('role_id','2')->where('device_type','2')->where('user_app_version',$user_ver->user_app_version)->count();
                $userCountArr1[] = $count;
                $userVersionArr1[] = $user_ver->user_app_version;
            }

            $pie1  =  Charts::create('pie', 'highcharts')
            ->title('IOS User App Version Report ('.$total_ios_count.')')
            ->labels($userVersionArr1)
            ->values($userCountArr1)
            ->dimensions(900,350)
            ->responsive(false);

            // $donut = Charts::create('donut', 'highcharts')
            //     ->title('My nice chart')
            //     ->labels(['First', 'Second', 'Third'])
            //     ->values([5,10,20])
            //     ->dimensions(450,250)
            //     ->responsive(false);

            $last  = date('Y-m-d', strtotime('today - 30 days'));
            for($i=1;$i<31;$i++){
                $dateArr[]= date('M d', strtotime($last. ' + '.$i.' days'));
                $dd = date('Y-m-d', strtotime($last. ' + '.$i.' days'));
                $usercountArr[] = User::whereDate('created_at',$dd)->count();
            }
            $line = Charts::create('line', 'highcharts')
                    ->title('Daily Register User')
                    ->elementLabel('User Count')
                    ->labels($dateArr)
                    ->values($usercountArr)
                    ->dimensions(900,350)
                    ->responsive(false);

            $paytm_requests = PaytmRequest::selectRaw('DATE(updated_at) as update_date')->where('status','2')->groupBy('update_date')->orderBy('update_date', 'asc')->where('created_at', '>=', $last)->get();
            $dateArr1= array(); 
            $paidAmtArr = array();

            foreach ($paytm_requests as $key => $value) { 
            $amountArr = PaytmRequest::selectRaw('SUM(amount) as paid_per_day_amount')->where('status','2')->whereDate('updated_at','=',$value->update_date)->first();
            $dateArr1[] = date('d M,Y',strtotime($value->update_date));
            $paidAmtArr[] = $amountArr->paid_per_day_amount;
            }

            $area = Charts::create('area', 'highcharts')
                    ->title('Daily Paid Payment Report')
                    ->elementLabel('Paid Amount')
                    ->labels($dateArr1)
                    ->values($paidAmtArr)
                    ->dimensions(900,350)
                    ->responsive(false);

            // $areaspline = Charts::multi('areaspline', 'highcharts')
            //               ->title('My nice chart')
            //               ->colors(['#ff0000', '#ffffff'])
            //               ->labels(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday','Saturday', 'Sunday'])
            //               ->dataset('John', [3, 4, 3, 5, 4, 10, 12])
            //               ->dataset('Jane',  [1, 3, 4, 3, 3, 5, 4]);

            // $geo = Charts::create('geo', 'highcharts')
            //         ->title('My nice chart')
            //         ->elementLabel('My nice label')
            //         ->labels(['ES', 'FR', 'RU','IN'])
            //         ->colors(['#C5CAE9', '#283593'])
            //         ->values([5,10,20,30])
            //         ->dimensions(600,500)
            //         ->responsive(false);

            // $percent = Charts::create('percentage', 'justgage')
            //             ->title('My nice chart')
            //             ->elementLabel('My nice label')
            //             ->values([65,0,100])
            //             ->responsive(false)
            //             ->height(300)
            //             ->width(0);

        $reward = Reward::where('status','1')->orderBy('value','asc')->get();
        foreach ($reward as $key => $value) {
           $wallet_count = Wallet::where('reward_id',$value->id)->whereMonth('redeem_date',date('m'))->whereYear('redeem_date',date('Y'))->count();
          // $wallet = Wallet::select('MONTH(redeem_date) as month')->groupBy('month')->count();
           $reward_count[] = $wallet_count;
           $rewardArr[] = $value->value;
        }



        $chart1 = Charts::create('bar', 'highcharts')
            ->title("Current Month Coupon Report")
            ->elementLabel("Total Coupon")
            ->dimensions(900, 350)
            ->responsive(false)
            ->labels($rewardArr)
            ->values($reward_count);

        foreach ($reward as $key => $value) {
           $wallet_count = Wallet::where('reward_id',$value->id)->whereDate('redeem_date',date('Y-m-d'))->count();
           $reward_count1[] = $wallet_count;
           $rewardArr1[] = $value->value;
        }

        $chart2 = Charts::create('bar', 'highcharts')
            ->title("Daily Coupon Report")
            ->elementLabel("Total Coupon")
            ->dimensions(900, 350)
            ->responsive(false)
            ->labels($rewardArr1)
            ->values($reward_count1);
        //return view('chart.chart',compact('chart','pie','donut','line','area','areaspline','geo','percent'));
        return view('chart.chart',compact('chart','line','area','chart1','chart2','pie','pie1'));
    }
}