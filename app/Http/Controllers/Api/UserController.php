<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Validator;
use App\User;
use Hash;
use App\Setting;
use App\RewardCoin;
use App\Step;
use App\Wallet;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function($request, $next){
          $this->user = Auth::user();
          return $next($request);
        });
    }

    public function update(Request $request, $id)
    {
        $user = $this->getUserById($id);

        if($user){
          $validate = Validator::make($request->all(), [
            'name' => 'required',
            'date_of_birth' => 'required',
            'gender' => 'required',
            //'phone_number' => 'required',
            'height' => 'required',
            //'height_unit' => 'required',
            'weight' => 'required',
            //'weight_unit' => 'required',
            //'exercise_level' => 'required'
          ]);

          if($validate->fails()){
            return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors(),'name' => $request->name), 422);
          }

          $user->name = $request->name;
          $user->date_of_birth = date('Y-m-d',strtotime($request->date_of_birth));
          $user->height = $request->height;
          //$user->height_unit = $request->height_unit;
          $user->weight = $request->weight;
          //$user->weight_unit = $request->weight_unit;
          //$user->exercise_level = $request->exercise_level;
          $user->gender = $request->gender;
          $user->phone_number = $request->phone_number;
          $user->save();

          return response()->json(array('message' => 'Profile has been updated successfully.', 'User' => $user), 200);
        }
        return response()->json(array('message' => 'User not found.'), 404);
    }

    public function updateImage(Request $request, $id)
    {
        $user = $this->getUserById($id);
        if($user){
          $validate = Validator::make($request->all(), [
            'image' => 'required'
          ]);

          if($validate->fails()){
            return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
          }

          if($request->image){
            $image_url = HelperController::imageUpload($request->image, 'user');
            if($image_url){
              $user->image = $image_url;
              $user->save();
            }
          }

          return response()->json(array('message' => 'Image has been updated successfully.', 'image' => $user->image, 'User' => $user), 200);
        }
        return response()->json(array('message' => 'User not found.'), 404);
    }

    public function getUserById($id)
    {
        return User::find($id);
    }

    public function updateFcmToken(Request $request)
    {
      $validate = Validator::make($request->all(), [
      'fcm_token' => 'required'
      ]);

      if($validate->fails()){
      return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
      }

      $user = User::find($this->user->id);
      $user->fcm_token = $request->fcm_token;
      $user->device_type = $request->device;
      $user->save();
      return response()->json(array('message' => 'Fcm token updated successfully.'), 200);
    }

   public function change_password(Request $request){

      if($this->user->id){
          $validate = Validator::make($request->all(), [
          'old_password'     => 'required',
          'new_password'     => 'required|min:6',
        ]);

      if($validate->fails()){
          return response()->json(array('message' => $validate->errors()->first(), 'errors' => $validate->errors()), 422);
        }


        $user = User::find($this->user->id);

        if(!Hash::check($request->old_password, $user->password)){

        return response()->json(array('message' => 'The specified password does not match the database password.','status' => 'error'), 422);
        }else{
        $user->update([
        'password' => Hash::make($request->new_password)
        ]); 
        return response()->json(array('message' => 'Your password has been changed successfully.','status' => 'success'), 200);
        } 
      }else{
          return response()->json(array('message' => 'User not found.','status' => 'error'), 404);
      }
    }

  public function getSetting(Request $request)
  {   
    
    if($request->app_version == '1.0.3'){
      //return response()->json(array('message' => 'We are under server maintenance. We will be back shortly. Please try again later.','link' => '','status' => 'error','button_text' => 'Try later'), 404);

      //return response()->json(array('message' => 'Your app version is not supported anymore. Please update your app to the newest version.','link' => 'https://play.google.com/store/apps/details?id=com.hexalab.walknearn','status' => 'error','button_text' => 'Update App'), 404);

      return response()->json(array('message' => 'Your app version is not supported anymore. Please uninstall and install the new version.','link' => 'https://play.google.com/store/apps/details?id=com.hexalab.walknearn','status' => 'error','button_text' => 'Get App'), 404);
      
    }

    $setting = Setting::find(1); 
    $user = User::find($this->user->id);
    $reward_ads_info = RewardCoin::where('user_id',$this->user->id)->where('reward_type','2')->orderBy('id','desc')->first();
    if($reward_ads_info){
      $reward_ads_info->ads_timestamp = strtotime($reward_ads_info->created_at);
    }
    
   // $reward_ads_info->ads_timestamp1 = microtime($reward_ads_info->created_at);
    $user->user_app_version = $request->app_version;

    $setting->lucky_coupon_text = 'Claim your lucky coupon in every one hour and get additional Sweat Coin to earn more.';
    $setting->reward_text = 'Watch rewarded ads now and earn '.$setting->reward_ads_coin.' Sweatcoins.'; 
    $setting->referal_text = 'Refer a friend & Earn '.$setting->frd_refferal_coin.' Sweatcoins when your friend Sign Up.';
    

   // $setting->reward_active = 0;
    $setting->current_time = date('Y-m-d H:i:s');
    $setting->reward_ads_info = $reward_ads_info;
   // $setting->condition = '<ol><li> The amount should be a maximum of 60 percent of the wallet balance.</li><li> It may take up to 24-72 working hours for the amount to get credited in your above mentioned PayTm account.</li><ol>';
    $setting->condition = '<ol><li> The amount should be a maximum of Rs.10.</li><li> It may take up to 24-72 working hours for the amount to get credited in your above mentioned PayTm or Google Pay account.</li><ol>';

    $setting->terms_condition = '<h1><strong><center>Privacy Policy</center></strong></h1><p></p>
        <p class="c0"><span class="c8">This document is an electronic record in terms of Information Technology Act, 2000
        and rules made thereunder and as the same may be amended from time to time. Being a system generated
        electronic record, it does not require any physical or digital signature.</span></p>
        <p></p>
        <p class="c0"><span class="c11">Greetings from Walk and Earn (</span><span class="c8">hereinafter referred to as the</span><span class="c11">&nbsp;&ldquo;</span><span>App</span><span class="c11">&rdquo;). The App is owned by
        Hexalab Software Pvt. Ltd., a company incorporated in India (</span><span class="c8">hereinafter referred to</span><span class="c11">&nbsp;</span><span class="c8">as</span><span class="c11">&nbsp;&ldquo;</span><span>We</span><span class="c11">&rdquo; or &ldquo;</span><span>Our</span><span class="c11">&rdquo; or &ldquo;</span><span>Us</span><span class="c11">&rdquo; or &ldquo;</span><span>Company</span><span class="c11">&rdquo;).
        </span></p>
        <p></p>
        <p class="c0"><span class="c11">We respect the privacy of our App users (&ldquo;</span><span>You</span><span class="c11">&rdquo; or &ldquo;</span><span>Your</span><span class="c11">&rdquo;) and the
        confidentiality of the information provided by You and have developed this Privacy Policy to demonstrate
        Our commitment to protecting the same. This Privacy Policy describes the type of information We collect,
        purpose, usage, storage and handling of such information, and disclosure thereof. We encourage You to read
        this Privacy Policy carefully when (i) using Our App from any computer, computer device, mobile, smartphone
        or any electronic device, or (ii) availing any products or services offered on or through the App. By using
        Our App, You are accepting the practices described in this Privacy Policy.</span></p>
        <p></p>
        <ol>
        <li ><span >WHAT INFORMATION DO WE COLLECT?</span></li>
        </ol>
        <p></p>
        <ol class="c6 lst-kix_list_3-1 start" start="1">
        <li><span class="c11">When You use the App, We collect Your personal and non-personal
        information. Your personal information helps Us to collect information that can directly identify You
        such as Your name, address, email address, phone number, personal biography information, photographs
        and/or payment information (&quot;</span><span>personal information</span><span>&quot;).
        We also collect Your non-personal information that does not directly identify You. By using the App,
        You are authorizing Us to collect, parse, store, process, disclose, disseminate and retain such
        information as envisaged herein. Unless specified otherwise in this Privacy Policy, Your personal
        information shall not be made public or made available to other users without Your explicit permission.</span></li>
        </ol>
        <p></p>
        <ol class="c6 lst-kix_list_3-1" start="2">
        <li><span class="c11">In order to register as a user with the App, You can either create an
        account with the App. Alternatively, You can sign in using Your Facebook/Google login. If You do so,
        You authorize Us to access certain Facebook/Google</span><span class="c2">&nbsp;</span><span class="c11">account
        information, such as Your public Facebook/Google</span><span class="c2">&nbsp;</span><span class="c11">profile
        (consistent with your privacy settings in Facebook/Google). You will also be asked to allow Us to
        collect Your location and movement (step count) information from the device when You use the App. In
        addition, We may collect and store any personal information You provide while using Our App or in some
        other manner. You may also provide Us photos, a personal description and information about Your gender
        and preferences</span><span class="c2">. </span><span>In case any chat facility is provided
        through the App and if You chat with other App users, You provide Us the content of Your chats, and If
        You contact Us for customer service or other inquiry, You provide Us with the content of that
        communication.</span></li>
        </ol>
        <p></p>
        <ol>
        <li><span>We neither knowingly collect any information nor promote Our App to any
        minor under the age of 18 (eighteen) years. If You are less than 18 (eighteen) years old or a minor in
        any other jurisdiction from where You access Our App, We request that You do not submit information to
        Us. If We become aware that a minor has registered with Us and provided Us with personal information,
        We may take steps to terminate such person&rsquo;s registration and delete their account with Us.
        </span></li>
        </ol>
        <p></p>
        <ol>
        <li><span class="c11">We use various tools and technologies, including cookies, to collect Your
        personal information and non-personal information from the device from which You access the App and
        learn about Your activities taking place under Your account when You use Our App.</span><span>&nbsp;</span><span class="c11">Such non-personal</span><span>&nbsp;</span><span>information could
        include Your IP address, device ID and type, Your browser type and language, operating system used by
        Your device, access times, Your device geographic location and the referring website address. We may
        use web beacons and other similar technologies to track Your use of Our App and to deliver or
        communicate with cookies. </span></li>
        </ol>
        <p ></p>
        <ol>
        <li><span>Advertising networks service providers, web traffic analysis service
        providers, Our business partners and vendors and other third parties may also use cookies, which is
        beyond Our control. These would most likely be analytical/performance cookies or targeting cookies.
        Blocking such cookies is possible by activating the appropriate settings on Your device browser.
        However this may affect the functionality of the App. </span></li>
        </ol>
        <p class="c0 c16 c17"><span></span></p>
        <ol class="c6 lst-kix_list_1-0" start="2">
        <li ><span >HOW WE USE THE INFORMATION WE COLLECT?</span></li>
        </ol>
        <p class="c0 c4"></p>
        <p class="c0 c19"><span class="c11">We may use information that We collect from You to:</span></p>
        <ol class="c6 lst-kix_list_4-0 start" start="1">
        <li><span>deliver and improve Our App and manage Our business;</span></li>
        <li><span>manage Your account and provide You with customer support;</span></li>
        <li><span>tracking of Your physical movement and location for step count and coin
        conversion;</span></li>
        <li><span>creating daily leaderboard rankings;</span></li>
        <li><span>providing access to offers and facilitating transactions for availing Ours
        and third party&rsquo;s goods and services through the App;</span></li>
        <li><span>perform research and analysis about Your use of, or interest in, Our or
        third party&rsquo;s products, services, or content or such products, services, or content as may be
        available on or through the App;</span></li>
        <li><span>communicate with You by email, postal mail, telephone and/or mobile devices
         about products or services that may be of interest to You either from Us or third parties or such
        products, services, or content as may be available on or through the App; </span></li>
        <li><span>develop, display, and track content and advertising tailored to Your
        interests on Our App, including providing advertisements to You;</span></li>
        <li><span>undertake App analytics;</span></li>
        <li><span>enforce or exercise any rights in our App Terms and Conditions; </span></li>
        <li><span>perform functions or services as otherwise described to You at the time of
        collection</span></li>
        <li><span>pay you through PayTm (For payment, we verify the QR code You provide and we pay using the same PayTm QR code).</span></li>
        </ol>
        <p class="c0 c4"></p>
        <ol class="c6 lst-kix_list_1-0" start="3">
        <li ><span >WITH WHOM WE SHARE YOUR INFORMATION?</span></li>
        </ol>
        <p></p>
        <ol class="c6 lst-kix_list_1-1 start" start="1">
        <li><span class="c11">When You register as a user of Our App, Your profile details (information
        You have provided Us directly or through your Facebook/Google</span><span class="c2">&nbsp;</span><span>account) will be accessible and viewable by other App users and Our business partners,
        sub-contractors, payment and delivery service providers, advertising networks, analytics providers,
        search information providers and credit reference agencies. </span></li>
        </ol>
        <p></p>
        <ol class="c6 lst-kix_list_1-1" start="2">
        <li><span>We do not share Your personal information with others except as indicated
        in this Privacy Policy or when We inform You and give You an opportunity to opt out of having Your
        personal information shared. </span></li>
        </ol>
        <p class="c0 c22"><span>&nbsp;</span></p>
        <ol class="c6 lst-kix_list_1-1" start="3">
        <li><span>We may also disclose Your personal information (i) for complying with
        applicable laws, requests or orders from law enforcement agencies, appropriate competent authorities or
        for any legal process; (ii) for enforcing the App Terms and Conditions; (iii) for protecting or
        defending Ours, any App user&rsquo;s or any third party&#39;s rights or property; (iv) for supporting
        any fraud/ legal investigation/ verification checks; or (v) in connection with a corporate transaction,
        including but not limited to sale of Our business, merger, consolidation, or in the unlikely event of
        bankruptcy. </span></li>
        </ol>
        <p class="c0 c22"><span class="c11">&nbsp;</span></p>
        <ol class="c6 lst-kix_list_1-1" start="4">
        <li><span>We may use and share Your non-personal information We collect under any of
        the above circumstances, including with third parties to develop and deliver targeted advertising on
        our App and on websites and/or applications of third parties, and to undertake analysis thereof. We may
        combine non-personal information We collect with additional non-personal information collected from
        other sources. We also may share aggregated, non-personal information, or personal information in
        hashed, non-human readable form, with third parties, including advisors, advertisers and investors, for
        the purpose of conducting general business analysis or other business purposes. </span></li>
        </ol>
        <p class="c0 c4"></p>
        <ol class="c6 lst-kix_list_1-0" start="4">
        <li ><span >HOW CAN YOU ACCESS OR CONTROL YOUR INFORMATION?</span></li>
        </ol>
        <p class="c0 c19 c16"></p>
        <ol class="c6 lst-kix_list_1-1" start="5">
        <li><span>If You have an App account with Us, You can review and update Your personal
        information by opening and editing Your profile details. In addition, We give You the control to opt
        out of having Your personal information shared, via the App settings. If You logout of Your App account
        or uninstall the App, We may retain certain information associated with Your account for analytical
        purposes and recordkeeping purposes or as required by law, as well as to prevent fraud, enforce our App
        Terms and Conditions, take actions We deem necessary to protect the integrity of Our App or other App
        users, or take other actions otherwise permitted by law. In addition, if certain information has
        already been provided to third parties as described in this Privacy Policy, retention of that
        information will be subject to those third parties&#39; policies. </span></li>
        </ol>
        <p class="c0 c16 c21"></p>
        <ol class="c6 lst-kix_list_1-1" start="6">
        <li><span>You can choose not to provide Us with certain information; however this may
        result in You being unable to use certain features of Our App. Our App may also deliver notifications
        to Your email or mobile device. You can avoid or disable these notifications by deleting the App or by
        making changed in the App settings. </span></li>
        </ol>
        <p class="c12"></p>
        <ol class="c6 lst-kix_list_1-1" start="7">
        <li><span>You are solely liable and responsible for any information You provide
        and/or share using the App.</span></li>
        </ol>
        <p class="c0 c21 c16"></p>
        <ol class="c6 lst-kix_list_1-0" start="5">
        <li ><span class="c3 c10">HOW DO WE PROTECT YOUR PERSONAL INFORMATION?</span></li>
        </ol>
        <p class="c0 c19 c16"></p>
        <p class="c0 c19"><span class="c11">We adopt reasonable security practices and procedures to help safeguard Your
        personal information under Our control from unauthorized access. However, You acknowledge that no Internet
        transmission or system or server can be 100% secure. Therefore, although We take all reasonable steps to
        secure Your personal information, We do not promise or guarantee the same, and You should not expect that
        Your personal information, or other communications while using the App will always remain secure and
        safeguarded by Us. You should always exercise caution while providing, sharing or disclosing Your personal
        information using the App. </span></p>
        <p class="c0 c4"></p>
        <ol class="c6 lst-kix_list_1-0" start="6">
        <li ><span >CHILDREN&#39;S PRIVACY.</span></li>
        </ol>
        <p class="c0 c19 c16"></p>
        <p class="c0 c19" id="h.gjdgxs"><span class="c11">Although Our App is a general audience App, We try Our best to
        restrict the use of Our App to individuals aged 18 (eighteen) years and above. We do not knowingly collect,
        maintain or use personal information from children under the age of 18 (eighteen) years.</span></p>
        <p class="c0 c4"></p>
        <ol class="c6 lst-kix_list_1-0" start="7">
        <li ><span >CHANGES TO THIS PRIVACY POLICY.</span></li>
        </ol>
        <p class="c0 c16 c19"></p>
        <p class="c0 c19"><span class="c11">We may occasionally update this Privacy Policy. When We post changes to this
        Privacy Policy, We will revise the &quot;last updated&quot; date. We recommend that You check Our App from
        time to time to keep Yourself updated of any changes in this Privacy Policy or any of Our other App related
        terms and policies.</span></p>
        <p class="c0 c4"></p>

        <p class="c0 c16 c24"></p>
        <ol class="c6 lst-kix_list_1-0" start="8">
        <li ><span >CONTACT US.</span></li>
        </ol>
        <p class="c0 c19 c16"></p>
        <p class="c0 c19"><span class="c11">Please contact Us by email on walkearn2019@gmail.com for any questions or
        comments regarding this Privacy Policy.</span></p>
        <p class="c0 c4"></p><a id="id.30j0zll"></a>
        <p class="c0 c4"></p>';

        $setting->how_it_works_text = '<h4>Sweat coins</h4>
<ul>
<li>&nbsp;We take step count from your Google Fit account</li>
<li>&nbsp;For each 100 steps you will get 1 Sweat coin</li>
<li>&nbsp;Pull to refresh for syncing your steps</li>
</ul>

<h4>Reward coins</h4>
<ul>
<li>&nbsp;Watch Rewarded ads after every 15 minutes</li>
<li>&nbsp;Watch Rewarded Video ads and get 5 Reward coins</li>
</ul>

<h4>Lucky coins</h4>
<ul>
<li>&nbsp;Get Lucky coupon after every 1 hour</li>
<li>&nbsp;Scratch lucky coupons and get bonus coins</li>
</ul>

<h4>Scratch Cards</h4>
<ul>
<li>&nbsp;Scratch cards will be available as per the earned Sweat coins</li>
<li>&nbsp;Currently 100, 200, 300, 500, 700 and 1000 scratch cards are available</li>
</ul>

<h4>Rewards</h4>
<ul>
<li>&nbsp;Claim your earned Sweat coins</li>
<li>&nbsp;Upon scratching the scratch card the earned amount will be added to your wallet</li>
<li>&nbsp;Withdraw the earned amount using PayTm or Google Pay methods</li>
</ul>

<h4>Withdrawal</h4>
<ul>
<li>&nbsp;Withdraw earned amount using PayTm or Google Pay methods</li>
<li>&nbsp;Provide active PayTm or Google Pay Phone number</li>
<li>&nbsp;Upload valid PayTm wallet or Google Pay QR code</li>
<li>&nbsp;Withdrawal amount for 1st request is upto a maximum of Rs.15</li>
<li>&nbsp;Withdrawal amount from 2nd request onwards is upto a maximum of Rs.10</li>
<li>&nbsp;Only one request can be made at a time until the request is completely processed</li>
<li>&nbsp;Upon rejection of the request, the respective amount will be credited to your wallet</li>
</ul>

<h4>Wallet</h4>
<ul>
<li>&nbsp;Check request status in Wallet</li>
<li>&nbsp;Upon rejection, the rejection reason will be mentioned</li>
</ul>';
   
    $wallet_info = Wallet::join('rewards','rewards.id','=','wallets.reward_id')->selectRaw("SUM(rewards.value) as redeem_coins")->where('user_id',$this->user->id)->first();
    $getRewardCoin = RewardCoin::selectRaw('SUM(coins) as total_coin')->where('user_id',$this->user->id)->first();
    if(!empty($wallet_info) && !empty($getRewardCoin)){
    
     // $user = User::find($this->user->id);
     // $totalsteps = Step::selectRaw('SUM(steps) as totalsteps')->where('user_id',$this->user->id)->orderBy('id','desc')->first();
      $totalsteps = Step::selectRaw('totalsteps')->where('user_id',$this->user->id)->orderBy('id','desc')->first();
      if(isset($totalsteps) && ($totalsteps->totalsteps > 0)){
        $user->total_steps = $totalsteps->totalsteps;  
        $totalcoins =floor($totalsteps->totalsteps/100);
        $user->redeem_coins = $wallet_info->redeem_coins;
        $get_redeem_coin = $getRewardCoin->total_coin + $totalcoins;
        $user->available_coins = $get_redeem_coin - $wallet_info->redeem_coins;
      }  
    }
    $user->save();
    $setting->user = $user;
    //lucky coupons

    if($setting->lucky_pop=='1'){
      $date = date('Y-m-d');
      $check = RewardCoin::where('user_id',$this->user->id)->where('reward_type','3')->whereDate('created_at','=',$date)->orderBy('id','desc')->first();
      $setting->lucky_coupon_info = $check;
      if(empty($check)){
        $setting->flag = '1';
      }else{
        $start_time = strtotime(date('H:i:s',strtotime($check->created_at)));
        $timediff = strtotime(date('H:i:s')) - $start_time;
        $setting->timediff = $timediff;
        
        if($timediff > 3600){
          $setting->flag = '1';
        }else{
          $setting->flag = '0';
        }
      }
      $setting->lucky_coins = $this->generate_slab($setting->lucky_min,$setting->lucky_max);
    }else{
      $setting->flag = '0';
    } 
    return $setting;
  }

  public function generate_slab($first,$last){

    return rand($first,$last);
  }

}
