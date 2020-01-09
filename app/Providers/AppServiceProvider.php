<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
//use Illuminate\Support\Facades\File;
//use Illuminate\Support\Facades\DB;

use Auth;
use App\User;
use App\Role;
use App\Wallet;
use App\PaytmRequest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         Schema::defaultStringLength(191);


        $admin_role = Role::where('role', 'Admin')->first();
        $user_count = User::where('role_id', '!=', $admin_role->id)->get()->count();
        //$verified_user_count = User::where('role_id', '!=', $admin_role->id)->where('email_verified_at','!=','')->get()->count();
        $verified_user_count = 12;
        $paid_paytm_amt = PaytmRequest::selectRaw('SUM(amount) as paytm_bal')->where('status','2')->first();
        $total_paytm_amt = $paid_paytm_amt->paytm_bal;

        // if(env('APP_DEBUG')) {
        //     DB::listen(function($query) {
        //         File::append(
        //             storage_path('/logs/query.log'),
        //             $query->sql . ' [' . implode(', ', $query->bindings) . ']' . PHP_EOL
        //        );
        //     });
        // }

        view()->composer('welcome', function($view) use($user_count,$total_paytm_amt){
          $view->with(['user_count' => $user_count,'total_paytm_amt' => $total_paytm_amt]);
        });
    }
}
