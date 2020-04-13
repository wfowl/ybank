<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function currency() 
    {
        return $this->belongsTo('App\Currency');
    }
}
