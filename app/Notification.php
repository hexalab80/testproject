<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Notification extends Model
{
    public function getCreatedAtAttribute($value)
    {
        if($value){
          return Carbon::createFromTimeStamp(strtotime($value))->diffForHumans();
        }
    }

    // public function getImageAttribute($value)
    // {
    //     if($value){
    //       return url($value);
    //     }
    // }
}
