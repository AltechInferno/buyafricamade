<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Models\User;
use Osen\Mpesa\STK;
use Osen\Mpesa\C2B;
use Osen\Mpesa\B2C;
use App\Models\Transaction;
use Session;
use Auth;

class MpesaController extends Controller
{
    /**
     * Create a new MpesaController instance. We also configure the M-PESA APIs here so they are available for the controller methods.
     *
     * @return void
     */
    public function __construct()
    {
        STK::init(
            array(
                'env'              => env('MPESA_ENV'),
                'type'             => 1,
                'shortcode'        => env('MPESA_SHORT_CODE'),
                'key'              => env('MPESA_CONSUMER_KEY'),
                'secret'           => env('MPESA_CONSUMER_SECRET'),
                'passkey'          => env('MPESA_PASSKEY'),
                'validation_url'   => url('lnmo/validate'),
                'confirmation_url' => url('lnmo/confirm'),
                'callback_url'     => url('lnmo/reconcile'),
                'results_url'      => url('lnmo/results'),
                'timeout_url'      => url('lnmo/timeout'),
            )
        );
        C2B::init(
            array(
                'env'              => env('MPESA_ENV'),
                'type'             => 1,
                'shortcode'        => env('MPESA_SHORT_CODE'),
                'key'              => env('MPESA_CONSUMER_KEY'),
                'secret'           => env('MPESA_CONSUMER_SECRET'),
                'passkey'          => env('MPESA_PASSKEY'),
                'validation_url'   => url('lnmo/validate'),
                'confirmation_url' => url('lnmo/confirm'),
                'callback_url'     => url('lnmo/reconcile'),
                'timeout_url'      => url('lnmo/timeout'),
                'result_url'       => url('lnmo/results'),
            )
        );
        B2C::init(
            array(
                'env'              => env('MPESA_ENV'),
                'type'             => 1,
                'shortcode'        => env('MPESA_SHORT_CODE'),
                'key'              => env('MPESA_CONSUMER_KEY'),
                'secret'           => env('MPESA_CONSUMER_SECRET'),
                'passkey'          => env('MPESA_PASSKEY'),
                'password'         => env('MPESA_PASSWORD'),
                'validation_url'   => url('lnmo/validate'),
                'confirmation_url' => url('lnmo/confirm'),
                'callback_url'     => url('lnmo/reconcile'),
                'timeout_url'      => url('lnmo/timeout'),
                'result_url'       => url('lnmo/results'),
            )
        );
    }
    public function pay()
    {
        if(Session::has('payment_type')){
            $transaction = new Transaction();
            $transaction->user_id = Auth::user()->id;
            $transaction->gateway = 'paytm';
            $transaction->payment_type = Session::get('payment_type');
            $transaction->additional_content = json_encode(Session::get('payment_data'));
            $transaction->save();

            Session::put('transaction_id', $transaction->id);
            
            if(Session::get('payment_type') == 'cart_payment'){
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                return view('frontend.mpesa.payment', compact('combined_order'));
            }
            elseif (Session::get('payment_type') == 'wallet_payment') {
                return view('frontend.mpesa.payment');
            }
            elseif (Session::get('payment_type') == 'customer_package_payment') {
                $customer_package_id = Session::get('payment_data')['customer_package_id'];
                $customer_package  = CustomerPackage::findOrFail($customer_package_id);
                return view('frontend.mpesa.payment', compact('customer_package'));
            }
            elseif (Session::get('payment_type') == 'seller_package_payment') {
                $seller_package_id = Session::get('payment_data')['seller_package_id'];
                $seller_package  = SellerPackage::findOrFail($seller_package_id);
                return view('frontend.mpesa.payment', compact('seller_package'));
            }
        }
    }

    public function payment_complete(Request $request)
    {
        $request->Msisdn   = (substr($request->Msisdn, 0, 1) == '+') ? str_replace('+', '', $request->Msisdn) : $request->Msisdn;
        $request->Msisdn   = (substr($request->Msisdn, 0, 1) == '0') ? preg_replace('/^0/', '254', $request->Msisdn) : $request->Msisdn;

        if(Session::has('payment_type')){
            if(Session::get('payment_type') == 'cart_payment'){
                $amount = CombinedOrder::find(Session::get('combined_order_id'))->grand_total;
            }
            if(Session::get('payment_type') == 'wallet_payment'){
                $amount = Session::get('payment_data')['amount'];
            }
            if(Session::get('payment_type') == 'customer_package_payment'){
                $payment_data = Session::get('payment_data');
                $customer_package_id = $payment_data['customer_package_id'];
                $amount = CustomerPackage::findOrFail($customer_package_id)->amount;
            }
            if(Session::get('payment_type') == 'seller_package_payment'){
                $payment_data = Session::get('payment_data');
                $seller_package_id = $payment_data['seller_package_id'];
                $amount = SellerPackage::findOrFail($seller_package_id)->amount;
            }

            try {
                $c2bTransaction = STK::send($request->Msisdn, round($amount), Session::get('transaction_id'));
                
            } catch (\Throwable $th) {
                return $th;
            }
    
            if(array_key_exists('errorMessage', $c2bTransaction)) {
                flash($c2bTransaction['errorMessage'])->error();
                return redirect(route('home'));
            }
    
            $transaction = Transaction::find(Session::get('transaction_id'));
            $transaction->mpesa_request    = $c2bTransaction['MerchantRequestID'];
            $transaction->save();
    
            try{
                if($c2bTransaction['ResponseCode'] != 0){
                    // fail or cancel or incomplete
                    Session::forget('payment_data');
                    flash(translate('Payment incomplete'))->error();
                    return redirect()->route('home');
    
                }
                else {
                    flash(translate('Payment request successfully submitted'))->success();
                    return redirect()->route('home');
                }
            }
            catch (\Exception $e) {
                flash(translate('Payment failed'))->error();
                  return redirect()->route('home');
            }
        }
        else{
            Session::forget('payment_data');
            flash(translate('Payment incomplete'))->error();
            return redirect()->route('home'); 
        }
    }


    public function reconcile(Request $request)
    {
        $response = isset($request['Body']) ? $request['Body'] : [];
        
        if($response != null && $response['stkCallback']['MerchantRequestID'] != null)
        {
            $merchantRequestID      = $response['stkCallback']['MerchantRequestID'];
            $CallbackMetadata       = $response['stkCallback']['CallbackMetadata']['Item'];
            $mpesaReceiptNumber     = $CallbackMetadata[1]['Value'];

            $transaction = Transaction::where('mpesa_request', $merchantRequestID)->first();
            if($transaction->status == 0 ){
                $transaction->mpesa_receipt = $mpesaReceiptNumber;
                $transaction->status        = 1;
                $transaction->save();

                $payment_type = json_decode($transaction->additional_content, true)['payment_type'];
                Auth::login(User::findOrFail($transaction->user_id));
                if ($payment_type == 'cart_payment') {
                    return (new CheckoutController)->checkout_done(json_decode($transaction->additional_content)->combined_order_id, json_encode($response));
                }
                elseif($payment_type == 'wallet_payment'){
                    return (new WalletController)->wallet_payment_done(json_decode($transaction->additional_content, true), json_encode($response));
                }
                elseif ($payment_type == 'customer_package_payment') {
                    return (new CustomerPackageController)->purchase_payment_done(json_decode($transaction->additional_content, true), json_encode($response));
                }
                elseif ($payment_type == 'seller_package_payment') {
                    return (new SellerPackageController)->purchase_payment_done(json_decode($transaction->additional_content, true), json_encode($response));
                }
            }
        }
    }

    public function timeout(Request $request)
    {
        return STK::timeout(
            function ($response)
            {
                return true;
            }
        );
    }

}
