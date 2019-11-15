<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
/** All Paypal Details class **/
use PayPal\Api\ItemList;
use App\User;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\Auth; 
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Redirect;
use Session;
use URL;
use Request as Req;
use App\PaymentInvoice;
use App\TransactionLogs;

class PaypalController extends Controller
{
    private $_api_context;
    private $payment_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
            $paypal_conf['client_id'],
            $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }
    public function index()
    {
        return view('paywithpaypal');
    }
    public function payWithpaypal(Request $request)
    {

        $data = $request->all();
        $user = User::where('mobile_number', $data['data']['mobile_number'])->first();

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item_1 = new Item();
        $item_1->setName('QR Jeep Balance') /** item name **/
            ->setCurrency('PHP')
            ->setQuantity(1)
            ->setPrice($data['data']['amount']); /** unit price **/
        $item_list = new ItemList();
        $item_list->setItems(array($item_1));
        $amount = new Amount();
        $amount->setCurrency('PHP')
            ->setTotal($data['data']['amount']);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');
        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::to('status')) /** Specify return URL **/
            ->setCancelUrl(URL::to('status'));
        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/
        try {
            $payment->create($this->_api_context);
        } catch (\PayPal\Exception\PPConnectionException $ex) {
            if (\Config::get('app.debug')) {
                \Session::put('error', 'Connection timeout');
                return Redirect::to('/');
            } else {
                \Session::put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::to('/');
            }
        }
        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        if (isset($redirect_url)) {
            /** redirect to paypal **/

            PaymentInvoice::create([
                'user_id' => $user->id,
                'paypal_id' => $payment->getId(),
                'amount' => $data['data']['amount'],
                'status' => 'pending'
            ]);

            return response()->json([
                'url' => $redirect_url
            ]);
        }
        \Session::put('error', 'Unknown error occurred');
        return Redirect::to('/');
    }
    public function getPaymentStatus()
    {

        $getPaymentInvoice = PaymentInvoice::where('paypal_id', Req::get('paymentId'))->first();

        if ($getPaymentInvoice->status == 'paid' || (empty(Req::get('PayerID')) || empty(Req::get('token')))) {
            return Redirect::to('/failed');
            exit;
        }
        $payment = Payment::get(Req::get('paymentId'), $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId(Req::get('PayerID'));
        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);
        if ($result->getState() == 'approved') {   


            if ($getPaymentInvoice->status == 'paid') {
                return Redirect::to('/failed');
                exit;
            }

            $getUserBalance = User::where('id', $getPaymentInvoice->user_id)->first();


            $updatePaymentInvoice = PaymentInvoice::where('paypal_id', $getPaymentInvoice->paypal_id)->update([
                'status' => 'paid'
            ]);

            if ($updatePaymentInvoice) {

                TransactionLogs::create(
                [
                'user_id' => $getPaymentInvoice->user_id, 
                'scanned_mobile_number' =>  $getUserBalance->mobile_number,
                'amount' => $getPaymentInvoice->amount 
                ]);

                
                User::where('id', $getPaymentInvoice->user_id)->update([
                    'balance' => $getUserBalance->balance + $getPaymentInvoice->amount
                ]);


            }

            

            return Redirect::to('/success');
            exit;
        }
        return Redirect::to('/failed');
        exit;
    }
}
