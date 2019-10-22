<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pin extends Model
{
    protected $fillable = [
        'user_id', 'pin'
    ];

    public function user()

    {
        return $this->belongsTo('App\User');

    }

    protected $dates = [
        'created_at',
        'updated_at',
        'login_until'
    ];

}
