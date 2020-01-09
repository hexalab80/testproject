<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Validator;
use App\User;
use App\Notification;

use App\Http\Controllers\Api\FcmNotificationController as FcmNotificationController;

class BroadcastController extends Controller
{
    public function index()
    {
        $notifications = Notification::all();
        return view('broadcast.index', ['notifications' => $notifications]);
    }

    public function create()
    {
        return view('broadcast.create');
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
          'description' => 'required'
        ])->validate();

        $user = User::all();
        $token = $user->pluck('fcm_token')->toArray();

        $notification = new Notification;
        if($request->file('image')){
          $path = $request->file('image')->store('notification', 'public');
          $image_url = $request->image->hashName();
          $notification->image = '/storage/notification/'.$image_url;
        }
        $notification->description = $request->description;
        $notification->save();

        $ios_token = User::where('device_type', '2')->get()->pluck('fcm_token')->toArray();
        $android_token = User::where('device_type', '1')->where('role_id','!=','1')->get()->pluck('fcm_token')->toArray();

        //$ios_token = User::whereIn('id', [153, 315, 255])->where('device', 'ios')->get()->pluck('fcm_token')->toArray();
        //$android_token = User::whereIn('id', [153, 315, 255])->where('device', 'android')->get()->pluck('fcm_token')->toArray();

        $notification->type = "broadcast";
        $notification->from_where = "broadcast";

        $action = "in.hexalab.walkandearn.activity.HomeActivity";

        //FcmNotificationController::sendNotificationForBroadcast($ios_token, $notification->toArray(), $action, '2');
        //FcmNotificationController::sendNotificationForBroadcast($android_token, $notification->toArray(), $action, '1');
        FcmNotificationController::sendNotification($android_token, $notification->toArray(), $action);

        request()->session()->flash('success', 'Notification sent successfully.');
        return redirect()->back();
    }
}
