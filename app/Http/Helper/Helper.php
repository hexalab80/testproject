<?php

namespace App\Http\Helper;

class Helper
{
  public static function imageBaseUrl($param_array, $dir)
  {
    if(is_a($param_array, 'Illuminate\Database\Eloquent\Collection')){
      foreach($param_array as $arr){
        if($arr->image){
          $arr->image =  request()->getHttpHost().'/storage/'.$dir.'/'.$arr->image;
        }
      }
    }
    else{
      if(count($param_array) > 1){
        $new_arr = [];
        foreach($param_array as $arr){
          if($arr['image']){
            $arr['image'] =  request()->getHttpHost().'/storage/'.$dir.'/'.$arr['image'];
          }
          array_push($new_arr, $arr);
        }
        return $new_arr;
        $param_array = $new_arr;
      }
      else{
        if($param_array->image){
          $param_array->image = request()->getHttpHost().'/storage/'.$dir.'/'.$param_array->image;
        }
      }
    }
    return $param_array;
  }

  public static function uploadFile($image, $dir)
  {
    $image = base64_decode(substr($image, strpos($image, ",")+1));
    if($image){
      $image_name = strtotime(date('y-m-d h:i:s')).mt_rand(1, 9999);
      $directory = public_path().'/storage/'.$dir;
      if(!file_exists($directory)){
        mkdir($directory, 0777);
      }

      $path = public_path().'/storage/'.$dir.'/'.$image_name.'.JPG';
      file_put_contents($path, $image);
      return $image_name.'.JPG';
    }
  }

  public static function sendSms($country_code, $mobile, $message)
  {
    $authkey = "229776A2y4cJoD5b644ef9";
    //$sender = "TLHCOR";
    $sender = "WAKERN";
    $route = 4;

    $sms_url = "http://api.msg91.com/api/sendhttp.php?authkey=".$authkey."&mobiles=".$mobile."&message=".$message."&sender=".$sender."&route=".$route."&country=".$country_code;
    $client = new \GuzzleHttp\Client();
    $res = $client->request('GET', $sms_url);
  }
}
