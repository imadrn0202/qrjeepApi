<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'plate_number', 'driver_user_id'
    ];

    public function user()

    {
        return $this->belongsTo('App\User', 'driver_user_id');

    }

    public function paymentLogs()
    {
        return $this->hasMany('App\PaymentLogs', 'driver_id', 'driver_user_id');
    }


}
