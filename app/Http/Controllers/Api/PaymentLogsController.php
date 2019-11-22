<?php

namespace App\Http\Controllers\Api;

use App\PaymentLogs;
use App\TransactionLogs;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Auth; 
use App\Http\Resources\PaymentLogs as PaymentLogsResource;
use App\Http\Resources\TransactionLogs as TransactionLogsResource;
use App\User;
use App\Driver;
use DB;
use Carbon\Carbon;
use App\Http\Resources\SelectedDriverFareLog as SelectedDriverFareLogResource;

class PaymentLogsController extends Controller
{

    public function getTotalEarnings() {
        $totalEarnings = Driver::where('user_id', Auth::user()->id)->sum('balance');

        $operatorEarnings = $totalEarnings * 0.65;

        $driverEarnings = $totalEarnings * 0.35;

        return response()->json([
            'total_earnings' => $totalEarnings,
            'operator_earnings' => $operatorEarnings,
            'driver_earnings' =>  $driverEarnings
        ]);
    }

    public function getOperatorTodayEarnings() {
        $operatorTodayEarnings = Driver::where('drivers.user_id', Auth::user()->id)
        ->join('payment_logs', 'payment_logs.driver_id', '=', 'drivers.id')
        ->whereDate('payment_logs.created_at', '=', Carbon::today()->toDateString())
        ->sum('payment_logs.final_amount');


        return response()->json([
            'operator_today_earnings' => $operatorTodayEarnings * 0.65
        ]);
    }


    public function getDriverTodayEarnings(Request $request) {
        $data = $request->all();

        $driverTodayEarnings = PaymentLogs::with('fare')->where('driver_id', $data['data']['driver_id'])
                                ->whereDate('payment_logs.created_at', '=', Carbon::today()->toDateString())
                                ->sum('final_amount');
        

        return response()->json([
            'driver_today_earnings' => $driverTodayEarnings * 0.35
        ]);

    }

    public function getSelectedDriverFareLog(Request $request) {
        $data = $request->all();

        $getDriverId = Driver::where('driver_user_id', Auth::user()->id)->first();

        $selectedDriverFareLog = PaymentLogs::with('fare')->where('driver_id', $getDriverId->driver_user_id)
                                ->orderBy('created_at', 'desc')
                                ->get();
        

        return SelectedDriverFareLogResource::collection($selectedDriverFareLog)->values()->all();

    }

    public function getUserPaymentLogs() {
        $paymentLogs = PaymentLogs::with('fare')->where('user_id', Auth::user()->id)->get();

        return PaymentLogsResource::collection($paymentLogs)->values()->all();
    }

    public function getUserTransactionLogs() {
        $transactionLogs = TransactionLogs::where('scanned_mobile_number', Auth::user()->mobile_number)->get();

        return TransactionLogsResource::collection($transactionLogs)->values()->all();
    }

}
