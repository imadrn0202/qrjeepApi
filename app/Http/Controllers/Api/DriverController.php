<?php

namespace App\Http\Controllers\Api;

use App\Driver;
use App\Http\Controllers\Controller;
use App\Http\Resources\DailyReportResource;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Resources\Driver as DriverResource;
use App\Http\Resources\GetDriverFareLogResource;
use App\Http\Resources\SelectedDriverFareLog;
use App\PaymentLogs;
use Carbon\Carbon;
use DB;

class DriverController extends Controller
{

    public function deleteDriver(Request $request) {
        $data = $request->all();

        $deleteDriver = Driver::where('driver_user_id', $data['driver_id'])->delete();

        PaymentLogs::where('driver_id', $data['driver_id'])->delete();

        User::where('id', $data['driver_id'])->delete();

        if (!$deleteDriver) {
            return response()->json([
                'success' => false
            ]);
        }
        return response()->json([
            'success' => true
        ]);
        

    }

    public function getDriverByDriverUserId(Request $request) {

        $data = $request->all();
        
        $driver = Driver::with('user')->where('driver_user_id', $data['data']['driver_user_id'])->first();

        return response()->json($driver);

    }

    public function updateDriver(Request $request) {
        $data = $request->except('mobile_number');

        $mobile = $request->only('mobile_number');




        $updateDriver = Driver::where('driver_user_id', $data['driver_user_id'])
        ->update($data);

        User::where('id', $data['driver_id'])
        ->update([
            'mobile_number' => $mobile['mobile_number'],
        ]);

        if (!$updateDriver) {
            return response()->json([
                'success' => false
            ]);
        }
        return response()->json([
            'success' => true
        ]);

    }


    public function getDriverList()
    {
        $drivers = Driver::with('user')->where('user_id', Auth::user()->id)->get();

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

        $selectedDriverFareLog = PaymentLogs::with('fare', 'driver')->where('driver_id', $getDriverId->driver_user_id)
            ->orderBy('created_at', 'desc')
            ->get();


        return GetDriverFareLogResource::collection($selectedDriverFareLog)->values()->all();
    }

    public function getDailyReport()
    {
        $selectedDriverFareLog = PaymentLogs::with('fare', 'driver')
        ->select('payment_logs.*', DB::raw('drivers.driver_user_id AS driver_user_id'), DB::raw('SUM(final_amount) AS earnings'))
        ->join('drivers', 'payment_logs.driver_id', '=', 'drivers.driver_user_id')
        ->where('drivers.user_id', Auth::user()->id)
        ->groupBy('payment_logs.driver_id', 'payment_logs.created_at')
        ->orderBy('payment_logs.created_at', 'desc')
        ->get();



        return SelectedDriverFareLog::collection($selectedDriverFareLog)->values()->all();


        // $getDriverId = Driver::with('paymentLogs')->join('payment_logs', 'drivers.driver_user_id', '=', 'payment_logs.driver_id')->where('drivers.user_id', Auth::user()->id)->get();
        
        // return DailyReportResource::collection($getDriverId)->values()->all();
    }
}
