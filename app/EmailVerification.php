<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $fillable = [
        'user_id', 'email', 'code'
    ];

    public function user()

    {
        return $this->belongsTo('App\User');

    }

    protected $dates = [
        'created_at',
        'updated_at',
        'email_verified_at'
    ];
}
