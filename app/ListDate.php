<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListDate extends Model
{
      public function listinfo()
    {
        return $this->hasMany('App\ListInfo');
    }
}
