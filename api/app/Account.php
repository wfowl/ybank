<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function country() 
    {
        return $this->belongsTo('App\Country')->get();
    }
}
