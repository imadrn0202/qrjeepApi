<?php

namespace App\Http\Controllers\Api;

use App\Pin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\EmailVerification;

class PinController extends Controller
{

    public function checkPin(Request $request) {


        $getPin = $request->all();

        $pin = $getPin['data']['pin'];

        $getUser = Pin::where('user_id', Auth::user()->id)->first();

       

        if (empty($getUser)) {
            Pin::create([
                'user_id' => Auth::user()->id,
                'pin' => $pin,
            ]);

            return response()->json([
                'message' => 'success',
                'verified' => true
            ]);
        }
        else {
           

            if ($getUser->pin != $pin) {
                Pin::where('user_id', Auth::user()->id)->update(['attempts' => DB::raw('attempts+1')]);

                if ($getUser->attempts >= 5) {
                    $locked_until = $getUser->login_until->diffForHumans(Carbon::now()->format('Y-m-d H:i:s'));

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
                 $locked_until = $getUser->login_until->diffForHumans(Carbon::now()->format('Y-m-d H:i:s'));
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


    public function updatePin(Request $request) {

        $data = $request->all();


        $code = $data['data']['code'];

        $checkIfValid = EmailVerification::where('user_id', Auth::user()->id)->where('code', $code)->first();

        if (empty($checkIfValid)) {
            return response()->json([
                'verified' => false
            ]);
        }
        else {
            //check if user has pin

            $pin = Pin::where('user_id', Auth::user()->id)->delete();

            return response()->json([
                'verified' => true
            ]);
        }

    }

    public function hasPin() {
        $checkIfHasPin = Pin::where('user_id', Auth::user()->id)->first();
        

        if (empty($checkIfHasPin)) {
            return response()->json([
                'message' => false
            ]);
        }
        else {
            return response()->json([
                'message' => true
            ]);
        }
    }
}
