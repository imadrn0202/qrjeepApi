<?php

namespace App\Http\Controllers\Api;

use App\FareMatrix;
use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Http\Resources\Destination as DestionationResource;
use App\Http\Resources\Origin as OriginResource;
use App\Http\Resources\Fare as FareResource;
use Illuminate\Support\Facades\Auth; 
use App\User;
use App\PaymentLogs;

class FareMatrixController extends Controller
{
    

    public function getOrigin() {

        $origin  = FareMatrix::get();


        return OriginResource::collection($origin)->unique('origin')->values()->all();
    }

    public function getDestination(Request $request) {
        
        $origin = $request->all();
        $destinations  = FareMatrix::where('origin', $origin['data']['origin'])->get();

       



        return DestionationResource::collection($destinations)->values()->all();

    }

    public function getFare(Request $request) {
        $origin = $request->all();
        $fare  = FareMatrix::where('origin', $origin['data']['origin'])->where('destination', $origin['data']['destination'])->first();

        $quantity = $origin['data']['qty'];
        $user_type = $origin['data']['type'];

        if ($user_type == 'regular')
        {
            $finalFare = $fare->fare * $quantity;

        }
        else {
            $discounted = $fare->fare * 0.20;
            $iniFare = ($fare->fare - $discounted) * $quantity;
            $finalFare = number_format((float)$iniFare, 2, '.', '');
        }

        return response()->json([
            'fare' => $finalFare
        ]);
    }

    public function payFare(Request $request)
    {
        $data = $request->all();

        $quantity = $data['data']['qty'];
        $driver_id = $data['data']['driver_id'];
        $user_type = $data['data']['type'];

        $fare = FareMatrix::where('origin', $data['data']['origin'])->where('destination', $data['data']['destination'])->first();

        //check if user has balance

        if ($user_type == 'regular')
        {
            $finalFare = $fare->fare * $quantity;
            $finalBalance = Auth::user()->balance - $finalFare;
        }
        else {
            $discounted = $fare->fare * 0.20;
            $iniFare = ($fare->fare - $discounted) * $quantity;
            $finalFare = number_format((float)$iniFare, 2, '.', '');
            $finalBalance = Auth::user()->balance - $finalFare;
        }


        if (Auth::user()->balance < $finalFare) {
            return response()->json([
                'message' => 'Not Enough Balance',
                'success' => false
            ]);
        }
        else {

            $getDriverBalance = User::where('mobile_number', $driver_id)->first();
           
            $updateUserBalance = User::where('id', Auth::user()->id)->update([
                'balance' => $finalBalance
            ]);

            $updateDriverBalance = User::where('mobile_number', $driver_id)->update([
                'balance' => $getDriverBalance->balance + $finalFare
            ]);

            PaymentLogs::create([
                'user_id' => Auth::user()->id,
                'fare_id' => $fare->id,
                'driver_id' => $driver_id,
                'user_type' => $user_type,
                'quantity' => $quantity,
                'final_amount' => $finalFare
            ]);

            return response()->json([
                'messsage' => 'You have successfully Paid',
                'success' => true
            ]);
        }

    }


}
