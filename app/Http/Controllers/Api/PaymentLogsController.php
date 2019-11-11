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

class PaymentLogsController extends Controller
{
    public function getUserPaymentLogs() {
        $paymentLogs = PaymentLogs::with('fare')->where('user_id', Auth::user()->id)->get();

        return PaymentLogsResource::collection($paymentLogs)->values()->all();
    }

    public function getUserTransactionLogs() {
        $transactionLogs = TransactionLogs::where('scanned_mobile_number', Auth::user()->mobile_number)->get();

        return TransactionLogsResource::collection($transactionLogs)->values()->all();
    }

}
