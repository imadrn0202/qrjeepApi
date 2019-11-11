<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile_number', 'pin', 'sms_verification_code', 'email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pin', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function findForPassport($username)
    {
        return $this->where('mobile_number', $username)->first();
    }

    public function tlogs()

    {

        return $this->hasOne('App\TransactionLogs', 'user_id', 'id');

    }

    public function pin()

    {

        return $this->hasOne('App\Pin', 'user_id', 'id');

    }

    public function paymentLogs()

    {

        return $this->hasOne('App\PaymentLogs', 'user_id', 'id');

    }

    public function email_verification()

    {

        return $this->hasOne('App\EmailVerification', 'user_id', 'id');

    }

    public function driver()

    {

        return $this->hasOne('App\Driver', 'user_id', 'id');

    }

}
