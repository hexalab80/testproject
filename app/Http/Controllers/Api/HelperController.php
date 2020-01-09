<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HelperController extends Controller
{
    public static function imageUpload($image, $dir)
    {
        $image_ext = substr($image, strpos($image, '/')+1, strpos($image, 'base64'));
        $image_ext = substr($image_ext, 0, strpos($image_ext, ';'));
        $image = base64_decode(substr($image, strpos($image, ',')+1));
        if($image && $image_ext){
          $image_name = strtotime(date('Y-m-d h:i:s')).mt_rand(1000, 9999);
          $directory = public_path().'/storage/'.$dir;
          if(!file_exists($directory)){
            mkdir($directory, 0777);
          }
          $path = $directory.'/'.$image_name.'.'.$image_ext;
          file_put_contents($path, $image);
          return $image_url = '/storage/'.$dir.'/'.$image_name.'.'.$image_ext;
        }
    }
}
