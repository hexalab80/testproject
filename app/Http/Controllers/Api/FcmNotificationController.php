<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class FcmNotificationController extends Controller
{
    public static function sendNotification($token, $data, $action = null)
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20)
                      ->setMutableContent(true);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setTitle('Walk & Earn')
                            ->setBody($data['description'])
                            ->setSound('default')
                            ->setClickAction($action);
                            // ->setBadge(1);

        $data['title'] = "Walk & Earn";
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($data);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        return $downstreamResponse->numberSuccess();
    }

    public static function sendNotificationForBroadcast($token, $data, $action = null, $device_type )
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20)
                      ->setMutableContent(true);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setTitle('Walk & Earn')
                            ->setBody($data['description'])
                            ->setSound('default')
                            ->setClickAction($action);
                            // ->setBadge(1);

        $data['title'] = "Walk & Earn";
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($data);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        if($device_type == "1"){
          $notification = null;
        }

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        return $downstreamResponse->numberSuccess();
    }

    public static function sendNotificationForPaytm($token, $data, $action = null, $device_type )
    {
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20)
                      ->setMutableContent(true);

        $notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setTitle($data['title'])
                            ->setBody($data['description'])
                            ->setSound('default')
                            ->setClickAction($action);
                            // ->setBadge(1);

        $data['title'] = $data['title'];
        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($data);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        if($device_type == "1"){
          $notification = null;
        }

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        return $downstreamResponse->numberSuccess();
    }
}
