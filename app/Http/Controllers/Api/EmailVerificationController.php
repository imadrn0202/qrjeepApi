<?php

namespace App\Http\Controllers\Api;

use App\EmailVerification;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;

class EmailVerificationController extends Controller
{
    public function addEmail(Request $request) {
        $data = $request->all();

        //add validation when email exist

        $email = $data['data']['email'];

        $getEmail = EmailVerification::where('email', $email)->first();


        if (empty($getEmail)) {

            $code = mt_rand(100000, 999999);

            $to_email = $email;
            $data = array('code' => $code);

            Mail::send('email.mail', $data, function($message) use ($to_email) {
            $message->to($to_email)
            ->subject('QRJeep Verification Code');
            $message->from('steampgames1@gmail.com','QRJeep');
            });


            EmailVerification::create([
                'user_id' => Auth::user()->id,
                'code' => $code,
                'email' => $email
            ]);

            return response()->json([
                'message' => 'verify'
            ]);


        }
        else {
            return response()->json([
                'message' => 'existing'
            ]);
        }

        
    }

    public function verifyEmail(Request $request) {
        $data = $request->all();

        $code = $data['data']['code'];

        $getEmail = EmailVerification::where('user_id', Auth::user()->id)->where('code', $code)->first();

        if (empty($getEmail)) {
            return response()->json([
                'verified' => false
            ]);
        }
        else {

            EmailVerification::where('user_id', Auth::user()->id)->where('code', $code)->update([
                'email_verified_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            return response()->json([
                'verified' => true
            ]);
        }



    }

    public function checkIfUserHasVerifiedEmail() {


        $getEmail = EmailVerification::where('user_id', Auth::user()->id)->whereNotNull('email_verified_at')->first();

        if (empty($getEmail)) {
            return response()->json([
                'verified' => false
            ]);
        }
        else {

            $code = mt_rand(100000, 999999);

            $to_email = $getEmail->email;

            $data = array('code' => $code);

            Mail::send('email.mail', $data, function($message) use ($to_email) {
            $message->to($to_email)
            ->subject('QRJeep Pin Verification Code');
            $message->from('steampgames1@gmail.com','QRJeep');
            });

            EmailVerification::where('user_id', Auth::user()->id)->whereNotNull('email_verified_at')->update([
                'code' => $code,
            ]);

            return response()->json([
                'verified' => true
            ]);
        }
    }
} 
