<?php


namespace App\Http\Controllers\Api\V2;

use App\Models\CombinedOrder;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Paystack;

class PaystackController extends Controller
{

    public function init(Request $request)
    {
        $paymentType = $request->payment_type;

        $amount = $request->amount;
        if ($paymentType == 'cart_payment') {
            $combined_order = CombinedOrder::find($request->combined_order_id);
            $amount = $combined_order->grand_total;
        }
        elseif($paymentType == 'order_re_payment') {
            $order = Order::find($request->order_id);
            $amount = $order->grand_total;
        }

        $user_id = $request->user_id;

        $user = User::find($user_id);
        $request->email = $user->email;
        $request->amount = round($amount * 100);
        $request->currency = env('PAYSTACK_CURRENCY_CODE', 'NGN');
        $request->reference = Paystack::genTranxRef();
        return Paystack::getAuthorizationUrl()->redirectNow();
    }


    // the callback function is in the main controller of web | paystackcontroller

    public function payment_success(Request $request)
    {
        try {

            $payment_type = $request->payment_type;

            if ($payment_type == 'cart_payment') {
                checkout_done($request->combined_order_id, $request->payment_details);
            }
            elseif ($request->payment_type == 'order_re_payment') {
                order_re_payment_done($request->order_id, 'Paystack', $request->payment_details);
            }
            elseif ($payment_type == 'wallet_payment') {
                wallet_payment_done($request->user_id, $request->amount, 'Paystack', $request->payment_details);
            }
            elseif ($payment_type == 'seller_package_payment') {
                seller_purchase_payment_done($request->user_id, $request->package_id, 'Paystack', $request->payment_details);
            }
            elseif ($payment_type == 'customer_package_payment') {
                customer_purchase_payment_done($request->user_id, $request->package_id, 'Paystack', $request->payment_details);
            }

            return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }
}
