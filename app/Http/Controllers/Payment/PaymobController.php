<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\Currency;
use App\Models\CustomerPackage;
use App\Models\Order;
use App\Models\SellerPackage;
use net\authorize\api\contract\v1\PaymentType;
use Session;
use Redirect;

class PaymobController extends Controller
{

    public function pay()
    {
        $currency_code = Session::has('currency_code') ? Session::get('currency_code') : Currency::findOrFail(get_setting('system_default_currency'))->code;
        if ($currency_code != "PKR") {
            flash(translate('Paymob Supports PKR Currency'))->error();
            return redirect()->route('cart');
        }

        if(Session::has('payment_type')) {
            $paymentType = Session::get('payment_type');
            $paymentData = Session::get('payment_data');

            if($paymentType == 'order_re_payment') {
                $order = Order::findOrFail($paymentData['order_id']);
                $amount = $order->grand_total;
            }
            if($paymentType == 'cart_payment') {
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $amount = $combined_order->grand_total;
            }
            elseif ($paymentType == 'wallet_payment') {
                $amount = $paymentData['amount'];
            }
            elseif ($paymentType == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
                $amount = $customer_package->amount;
            }
            elseif ($paymentType == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail($paymentData['seller_package_id']);
                $amount = $seller_package->amount;
            }
        }
    
        try {
            $token = $this->getToken();
            $order = $this->createOrder($token, $amount);
            $paymentToken = $this->getPaymentToken($order, $token, $amount);
        } catch (\Exception $exception) {
            flash(translate('Country Permission Denied or Misconfiguration'))->error();
            return redirect()->route('cart');
        }
        return \Redirect::away(
            "https://pakistan.paymob.com/api/acceptance/iframes/".env('PAYMOB_IFRAME_ID')."?payment_token=".$paymentToken
        );
    }

    public function getToken()
    {
        $response = $this->cURL("https://pakistan.paymob.com/api/auth/tokens", [
            "api_key" => env('PAYMOB_API_KEY'),
        ]);

        return $response->token;
    }

    public function createOrder($token, $amount)
    {
        $data = [
            "auth_token" => $token,
            "delivery_needed" => "false",
            "amount_cents" => round($amount, 2) * 100,
            "currency" => "PKR",
            "items" => [],
        ];
        $response = $this->cURL(
            "https://pakistan.paymob.com/api/ecommerce/orders",
            $data
        );

        return $response;
    }

    public function getPaymentToken($order, $token, $amount)
    {
        $user = auth()->user();
        $billingData = [
            "apartment" => "NA",
            "email" => $user->email ?? 'customer@example.com',
            "floor" => "NA",
            "first_name" => $user->name,
            "street" => "NA",
            "building" => "NA",
            "phone_number" => $user->phone ?? "+86(8)9135210487",
            "shipping_method" => "PKG",
            "postal_code" => "NA",
            "city" => "NA",
            "country" => "NA",
            "last_name" => $user->name,
            "state" => "NA",
        ];
        $data = [
            "auth_token" => $token,
            "amount_cents" => round($amount, 2) * 100,
            "expiration" => 3600,
            "order_id" => $order->id,
            "billing_data" => $billingData,
            "currency" => "PKR",
            "integration_id" => env('PAYMOB_INTEGRATION_ID'),
        ];

        $response = $this->cURL(
            "https://pakistan.paymob.com/api/acceptance/payment_keys",
            $data
        );

        return $response->token;
    }

    protected function cURL($url, $json)
    {
        // Create curl resource
        $ch = curl_init($url);

        // Request headers
        $headers = [];
        $headers[] = "Content-Type: application/json";

        // Return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // $output contains the output string
        $output = curl_exec($ch);

        // Close curl resource to free up system resources
        curl_close($ch);
        return json_decode($output);
    }

    public function callback(Request $request)
    {
        $data   = $request->all();
        ksort($data);
        $hmac   = $data["hmac"];
        $array  = [
            "amount_cents",
            "created_at",
            "currency",
            "error_occured",
            "has_parent_transaction",
            "id",
            "integration_id",
            "is_3d_secure",
            "is_auth",
            "is_capture",
            "is_refunded",
            "is_standalone_payment",
            "is_voided",
            "order",
            "owner",
            "pending",
            "source_data_pan",
            "source_data_sub_type",
            "source_data_type",
            "success",
        ];
        $connectedString = "";
        foreach ($data as $key => $element) {
            if (in_array($key, $array)) {
                $connectedString .= $element;
            }
        }
        $secret = env('PAYMOB_HMAC');
        $hased  = hash_hmac("sha512", $connectedString, $secret);


        if ($hased == $hmac && $data["success"] == "true") {
            if($request->session()->has('payment_type')){
                $paymentType = $request->session()->get('payment_type');
                $paymentData = $request->session()->get('payment_data');

                if($paymentType == 'cart_payment'){
                    return (new CheckoutController)->checkout_done($request->session()->get('combined_order_id'), json_encode($data));
                }
                elseif ($paymentType == 'order_re_payment') {
                    return (new CheckoutController)->orderRePaymentDone($paymentData, json_encode($data));
                }
                elseif ($paymentType == 'wallet_payment') {
                    return (new WalletController)->wallet_payment_done($paymentData, json_encode($data));
                }
                elseif ($paymentType == 'customer_package_payment') {
                    return (new CustomerPackageController)->purchase_payment_done($paymentData, json_encode($data));
                }
                elseif ($paymentType == 'seller_package_payment') {
                    return (new SellerPackageController)->purchase_payment_done($paymentData, json_encode($data));
                }
            }
        }

        flash(translate('Payment failed'))->error();
    	return redirect()->route('cart');        
    }
}
