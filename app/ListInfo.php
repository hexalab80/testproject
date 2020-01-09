<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ListInfo extends Model
{
    public function list()
    {
        return $this->belongsTo('App\ListDate','list_date_id');
    }
}
