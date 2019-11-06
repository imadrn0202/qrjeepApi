<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FareMatrix extends Model
{
    public function paymentLogs()
    {
        return $this->hasOne('App\Profile', 'fare_id', 'id');
    }

}
