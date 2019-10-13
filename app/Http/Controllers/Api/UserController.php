<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Carbon\Carbon;
use Nexmo;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    public function checkMobileNumber(Request $request) { 

        $mobileNumber = $request->all();

        $checkUser = User::where('mobile_number', $mobileNumber['mobile_number'])->get();

        $smscode = mt_rand(100000, 999999);


        if ($checkUser->isEmpty()) {

            $newUser = new User;
            $newUser->mobile_number = $mobileNumber['mobile_number']['mobile_number'];
            $newUser->sms_verification_code = $smscode;

            
            if($newUser->save()) {
                $success = true;
            }
            else {
                $success = false;
            }
            
        }
        else {

            $updateSmsCode =  User::where('mobile_number',  $mobileNumber['mobile_number'])->update(['sms_verification_code' => $smscode]);

            if($updateSmsCode) {
                $success = true;
            }
            else {
                $success = false;
            }

        }

        $strMobileNumber = implode("",$mobileNumber['mobile_number']);

        Nexmo::message()->send([
            'to'   => $strMobileNumber,
            'from' => 'NXSMS',
            'text' => 'Verification Code: '.$smscode.' '
        ]);
        

        return response()->json([
            'mobile_number' => $mobileNumber['mobile_number'],
            'success' => $success
        ]);

    
    }

    public function validateLogin(Request $request) {
        $verify = $request->all();

        $verifyCode = User::where('mobile_number', $verify['verification_code']['mobile_number'])->where('sms_verification_code', $verify['verification_code']['sms_verification_code'])->first();
        //$verifyCode = User::where('mobile_number', $verify['mobile_number'])->where('sms_verification_code', $verify['sms_verification_code'])->first();

        if (empty($verifyCode)) {
            return response()->json([
                'verified' => false
            ]);
        }

        else {

            $token = $verifyCode->createToken('Laravel Password Grant Client')->accessToken;
            
            return response()->json([
                'access_token' => $token,
                'verified' => true
            ]);

        }

        
    }

}
