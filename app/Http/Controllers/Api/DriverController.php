<?php

namespace App\Http\Controllers\Api;

use App\Driver;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\Driver as DriverResource;
use App\Http\Resources\SelectedDriverFareLog;
use App\PaymentLogs;
use Carbon\Carbon;

class DriverController extends Controller
{

    public function getDriverList()
    {
        $drivers = Driver::where('user_id', Auth::user()->id)->get();

        return DriverResource::collection($drivers)->values()->all();
    }

    public function createDriver(Request $request)
    {
        $data = $request->all();

        $smscode = mt_rand(100000, 999999);

        $checkUser = User::where('mobile_number', $data['data']['mobile_number'])->get();

        if ($checkUser->isEmpty()) {
            $newUser = new User;
            $newUser->mobile_number = $data['data']['mobile_number'];
            $newUser->sms_verification_code = $smscode;
            $newUser->save();


            $insertedId = $newUser->id;
        } else {
            return response()->json([
                'success' => false
            ]);
        }



        Driver::create([
            'user_id' => Auth::user()->id,
            'driver_user_id' => $insertedId,
            'first_name' => $data['data']['first_name'],
            'last_name' => $data['data']['last_name'],
            'plate_number' => $data['data']['plate_number']
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function getDriverEarnings()
    {

        $getDriverId = Driver::where('driver_user_id', Auth::user()->id)->first();

        if (empty($getDriverId)) {
            return response()->json([
                'success' => false
            ]);
        }


        $driverTotalEarnings = PaymentLogs::with('fare')->where('driver_id', $getDriverId->driver_user_id)
            ->sum('final_amount');


        $driverTodayEarnings = PaymentLogs::with('fare')->where('driver_id', $getDriverId->driver_user_id)
            ->whereDate('payment_logs.created_at', '=', Carbon::today()->toDateString())
            ->sum('final_amount');




        return response()->json([
            'driver_total_earnings' => $driverTotalEarnings * 0.35,
            'driver_today_earnings' => $driverTodayEarnings * 0.35
        ]);
    }

    public function getDriverFareLog()
    {

        $getDriverId = Driver::where('driver_user_id', Auth::user()->id)->first();

        if (empty($getDriverId)) {
            return response()->json([
                'success' => false
            ]);
        }


        $selectedDriverFareLog = PaymentLogs::with('fare')->where('driver_id', $getDriverId->id)
            ->orderBy('created_at', 'desc')
            ->get();


        return SelectedDriverFareLog::collection($selectedDriverFareLog)->values()->all();
    }
}
