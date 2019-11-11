<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FareMatrix extends Model
{
    public function paymentLogs()
    {
        return $this->hasMany('App\PaymentLogs', 'fare_id', 'id');
    }

}
