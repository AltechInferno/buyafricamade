<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use App\Models\Order;
use App\Utility\PayfastUtility;
use Illuminate\Http\Request;

class PayfastController extends Controller
{

    public function pay(Request $request)
    {
        $payment_type       = $request->payment_type;
        $combined_order_id  = $request->combined_order_id;
        $amount             = $request->amount;
        $user_id            = $request->user_id;
        
        if ($payment_type == 'cart_payment') {
            $combined_order = CombinedOrder::findOrFail($combined_order_id);
            return PayfastUtility::create_checkout_form($combined_order->id, $combined_order->grand_total, $payment_type, 'api');
        } elseif ($payment_type == 'order_re_payment') {
            $order = Order::findOrFail($request->order_id);
            return PayfastUtility::create_checkout_form($order->id, $order->grand_total, $payment_type, 'api');
        } elseif ($payment_type == 'wallet_payment') {
            return PayfastUtility::create_wallet_form($user_id, $amount, $payment_type, 'api');
        } elseif ($payment_type == 'customer_package_payment') {
            return PayfastUtility::create_customer_package_form($user_id, $request->package_id, $amount, $payment_type, 'api');
        } elseif ($payment_type == 'seller_package_payment') {
            return PayfastUtility::create_seller_package_form($user_id, $request->package_id, $amount, $payment_type, 'api');
        }
    }

    // =========================================================================
    // =========================================================================
    public function payfast_notify(Request $request)
    {

        // Tell PayFast that this page is reachable by triggering a header 200
        header('HTTP/1.0 200 OK');
        flush();
        $pfData = $_POST;

        if ($_POST['payment_status'] == "COMPLETE") {
            return self::payfast_success($_POST);
        }

        return $this->incomplete();
    }
    public static function payfast_success($response)
    {
        $payment_type = $response['custom_str3'];

        if ($payment_type == 'cart_payment') {
            $order_id = $response['custom_str1'];
            checkout_done($order_id, json_encode($response));
        }
        elseif ($payment_type == 'order_re_payment') {
            $order_id = $response['custom_str1'];
            order_re_payment_done($order_id, 'PayFast', json_encode($response));
        }
        elseif ($payment_type == 'wallet_payment') {
            $user_id = $response['custom_str1'];
            $amount = $response['amount_gross'];
            wallet_payment_done($user_id, $amount, 'PayFast', json_encode($response));
        }
        elseif ($payment_type == 'seller_package_payment') {
            $user_id = $response['custom_str1'];
            $package_id = $response['custom_str2'];
            $amount = $response['amount_gross'];
            seller_purchase_payment_done($user_id, $package_id, 'PayFast', json_encode($response));
        }
        elseif ($payment_type == 'customer_package_payment') {
            $user_id = $response['custom_str1'];
            $package_id = $response['custom_str2'];
            customer_purchase_payment_done($user_id, $package_id, 'PayFast', json_encode($response));
        }
        return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
    }
    public static function payfast_return(Request $request)
    {

        return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
    }

    public static function payfast_cancel()
    {
        return self::incomplete();
    }

    public static function incomplete()
    {
        return response()->json(['result' => false, 'url' => '', 'message' => "Incomplete payment"]);
    }
    // =========================================================================
    // =========================================================================


}
