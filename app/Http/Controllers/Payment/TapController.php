<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use App\Models\CombinedOrder;
use App\Models\Currency;
use App\Models\CustomerPackage;
use App\Models\Order;
use App\Models\SellerPackage;
use Auth;
use Illuminate\Http\Request;
use Redirect;
use Session;

class TapController extends Controller
{
    public function pay(){

        $amount = 0;
        $currency_code = Session::has('currency_code') ? Session::get('currency_code') : Currency::findOrFail(get_setting('system_default_currency'))->code;
        if (Session::has('payment_type')) {
            $paymentType = Session::get('payment_type');
            $paymentData = Session::get('payment_data');
            if ($paymentType == 'cart_payment') {
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $amount = round($combined_order->grand_total);
            } elseif ($paymentType == 'order_re_payment') {
                $order = Order::findOrFail($paymentData['order_id']);
                $amount = round($order->grand_total);
            } elseif ($paymentType == 'wallet_payment') {
                $amount = round(Session::get('payment_data')['amount']);
            } elseif ($paymentType == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
                $amount = round($customer_package->amount);
            } elseif ($paymentType == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail($paymentData['seller_package_id']);
                $amount = round($seller_package->amount);
            }
        }

        $requestbody = array(
            'amount' => $amount,
            'currency' => $currency_code,
            'threeDSecure' => true,
            "save_card" => false,
            "customer_initiated" => true,
            'description' => str_replace("_", " ", $paymentType),
            'customer' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email != null ? Auth::user()->email : 'test@test.com',
            ],
            // 'merchant' => [
            //     'id' => env('TAP_MERCHANT_ID')
            // ],
            'source' => [
                'id' => 'src_all'
            ],
            'post' => [
                'url' => null
            ],
            'redirect' => [
                'url' => route('tap.callback')
            ]
        );

        $requestbodyJson = json_encode($requestbody);

        $header = array(
            "Authorization: Bearer ".env('TAP_SECRET_KEY'),
            "accept: application/json",
            "content-type: application/json"
        );

        $url = curl_init("https://api.tap.company/v2/charges/");
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_ENCODING, "");
        curl_setopt($url, CURLOPT_MAXREDIRS, 10);
        curl_setopt($url, CURLOPT_TIMEOUT, 30);
        curl_setopt($url, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = json_decode(curl_exec($url));
        curl_close($url);

        if (isset($response->errors)) {
            flash($response->errors[0]->description)->warning();
            return redirect()->route('home');
        } else {
            if ($response->status == 'INITIATED') {
                return Redirect::to($response->transaction->url);
            }
            flash(translate('Payment failed'))->error();
            return redirect()->route('home');
        }
    }

    public function callback(Request $request){

        $header = array(
            "Authorization: Bearer ".env('TAP_SECRET_KEY'),
            "accept: application/json"
        );

        $url = curl_init("https://api.tap.company/v2/charges/".$request['tap_id']);
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_ENCODING, "");
        curl_setopt($url, CURLOPT_MAXREDIRS, 10);
        curl_setopt($url, CURLOPT_TIMEOUT, 30);
        curl_setopt($url, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = json_decode(curl_exec($url));
        curl_close($url);

        if (isset($response->errors)) {
            flash($response->errors[0]->description)->warning();
            return redirect()->route('home');
        } else {
            if ($response->status == 'CAPTURED') {
                $payment_type = Session::get('payment_type');
                $paymentData = Session::get('payment_data');
                if ($payment_type == 'cart_payment') {
                    return (new CheckoutController)->checkout_done(Session::get('combined_order_id'), json_encode($response));
                }
                else if ($payment_type == 'order_re_payment') {
                    return (new CheckoutController)->orderRePaymentDone($paymentData, json_encode($response));
                }
                else if ($payment_type == 'wallet_payment') {
                    return (new WalletController)->wallet_payment_done($paymentData, json_encode($response));
                }
                else if ($payment_type == 'customer_package_payment') {
                    return (new CustomerPackageController)->purchase_payment_done($paymentData, json_encode($response));
                }
                else if ($payment_type == 'seller_package_payment') {
                    return (new SellerPackageController)->purchase_payment_done($paymentData, json_encode($response));
                }
            }

            flash(translate('Payment failed'))->error();
            return redirect()->route('home');
        }
    }
}
