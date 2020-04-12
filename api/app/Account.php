<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $attributes = ['test'=> false];

    public $timestamps = false;

    public function country() 
    {
        return $this->belongsTo('App\Country');
    }

    public function withdraw($amount)
    {
        $this->balance = $this->balance - $amount;
        $this->save();
    }

    public function deposit($amount)
    {
        $this->balance = $this->balance + $amount;
        $this->save();
    }
}
