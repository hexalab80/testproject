<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
//use App\Role;
//use Auth;
//use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
    // use AuthenticatesUsers {
    //   logout as performLogout;
    // }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        //$this->admin_role = Role::where('role', 'Admin')->first();
        //$this->user_role = Role::where('role', 'Customer')->first();
    }

   /* public function authenticated(Request $request, User $user)
    {
      if($user->role_id == $this->user_role->id){
        if($user->email_verified_at == 0){

          $link = url("/verifyemail/".$user->id);
              Mail::to($user)->send(new VerifyEmailLink($user, $link));
              
          Auth::logout();
          //return response()->json(array('message' => 'Your email is not verified. Please verify.'), 409);
          return response()->json(array('message' => 'Please verify your email address. Verfication Link has been sent to your registered email.'), 422);
        }
        else if($user->status == 1){

          foreach($user->tokens as $token){
            $token->revoked = true;
            $token->save();
          }
          $user->makeHidden('tokens');

          $access_token = $user->createToken('access token')->accessToken;
          $authorization = "Bearer ".$access_token;
          $request->session()->put('authorization', $authorization);
          return response()->json(array('message' => 'Success', 'authorization' => $authorization, 'role' => 1), 200);
        }
        else if($user->status == 0){
          Auth::logout();
          return response()->json(array('message' => 'Your account is under verification. Please wait for approval.'), 403);
        }
        else if($user->status == 2){
          Auth::logout();
          return response()->json(array('message' => 'Your account has been blocked. Contact Walk & Earn.'), 403);
        }
      }
      else if($user->role_id == $this->admin_role->id){
        return redirect()->intended('/admin');
      }
    }

    public function logout(Request $request)
    {
        $this->performLogout($request);
        return redirect()->intended('/');
    }*/
}
