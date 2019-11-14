<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentInvoice extends Model
{
    protected $fillable = [
        'user_id', 'paypal_id', 'amount', 'status'
    ];

    public function user()

    {
        return $this->belongsTo('App\User');

    }
}
