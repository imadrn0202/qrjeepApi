<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionLogs extends Model
{
    protected $fillable = [
        'user_id', 'scanned_mobile_number', 'amount'
    ];

    public function user()

    {
        return $this->belongsTo('App\User');

    }

}

