<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use Carbon\Carbon;
use Nexmo;
use App\TransactionLogs;
use App\Pin;
use Illuminate\Support\Facades\DB;

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
                'mobile_number' => $verifyCode->mobile_number,
                'verified' => true
            ]);

        }

        
    }

    public function addBalance(Request $request) {

        $req = $request->all();


        $getCurrentBalance = User::where('mobile_number', $req['data']['scan_mobile_number'])->first();

        $initialAmount = $req['data']['amount'];
        $finalAmount = $getCurrentBalance->balance + $req['data']['amount'];
        $mob = $req['data']['scan_mobile_number'];

        

        $user = User::where('mobile_number', $req['data']['scan_mobile_number'])->update(['balance' => $finalAmount]);
        //$user = User::where('mobile_number', $req['scan_mobile_number'])->update(['balance' => $finalAmount]);



        $flight = TransactionLogs::create(
        [
        'user_id' => Auth::user()->id, 
        'scanned_mobile_number' =>  $mob,
        'amount' => $initialAmount 
        ]);

        return response()->json([
                'success' => true
        ]);

    }

    public function checkBalance() {


        $getCurrentBalance = User::where('mobile_number', Auth::user()->mobile_number)->first();


        return response()->json([
                'balance' => $getCurrentBalance->balance,
                'success' => true
        ]);

    }

    public function checkPin(Request $request) {


        $getPin = $request->all();

        $pin = $getPin['data']['pin'];

        $getUser = Pin::where('user_id', Auth::user()->id)->first();

       

        if (empty($getUser)) {
            Pin::create([
                'user_id' => Auth::user()->id,
                'pin' => $pin,
            ]);
        }
        else {
            $locked_until = $getUser->login_until->diffForHumans(Carbon::now()->format('Y-m-d H:i:s'));

            if ($getUser->pin != $pin) {
                Pin::where('user_id', Auth::user()->id)->update(['attempts' => DB::raw('attempts+1')]);

                if ($getUser->attempts >= 5) {

                    //dont add minute if theres existing block 
                    if ($getUser->login_until >= Carbon::now()->format('Y-m-d H:i:s'))
                    {
                        return response()->json([
                            'message' => 'locked',
                            'locked_until' => $locked_until,
                            'verified' => false
                        ]);
                    }
                    else
                    {
                        Pin::where('user_id', Auth::user()->id)->update(['login_until' => Carbon::now()->addMinutes(1)->format('Y-m-d H:i:s')]);
                        return response()->json([
                            'message' => 'locked_until',
                            'verified' => false
                        ]);
                    }
                }

                return response()->json([
                    'message' => 'wrong pin',
                    'verified' => false
                ]);
                
            }
            else if($getUser->pin == $pin && $getUser->login_until >= Carbon::now()->format('Y-m-d H:i:s')) {
      
                return response()->json([
                    'message' => 'locked',
                    'locked_until' => $locked_until,
                    'verified' => false
                ]);
            }
            else {
                Pin::where('user_id', Auth::user()->id)->update(['attempts' => 0]);

                return response()->json([
                    'message' => 'success',
                    'verified' => true
                ]);

            }
        }

    }

        

}
