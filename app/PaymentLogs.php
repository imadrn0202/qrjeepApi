<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentLogs extends Model
{
    protected $fillable = [
        'user_id', 'fare_id', 'final_amount', 'driver_id' ,'user_type' ,'quantity'
    ];

    public function user()

    {
        return $this->belongsTo('App\User');

    }

    public function fare()

    {
        return $this->belongsTo('App\FareMatrix');

    }



    public function driver()

    {
        return $this->belongsTo('App\Driver');

    }

}
