<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class PaytmRequest extends Model
{
    public function user_info()
    {
      return $this->belongsTo('App\User','user_id');
    }

    // public function getImageAttribute($value)
    // {
    //     if($value){
    //       return url($value);
    //     }
    // }

    public function getCreatedAtAttribute($value)
    {
        if($value){
          return Carbon::createFromTimeStamp(strtotime($value))->diffForHumans();
        }
    }

    // public function getUpdatedAtAttribute($value)
    // {
    //     if($value){
    //       return Carbon::createFromTimeStamp(strtotime($value))->diffForHumans();
    //     }
    // }
}
