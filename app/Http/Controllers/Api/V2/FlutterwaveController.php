<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\CombinedOrder;
use App\Models\Order;
use App\Models\User;
use Exception;
use Rave as Flutterwave;
use Illuminate\Http\Request;


class FlutterwaveController extends Controller
{

    public function getUrl(Request $request)
    {
        $payment_type = $request->payment_type;
        $user_id = $request->user_id;

        if ($payment_type == 'cart_payment') {
            $combined_order = CombinedOrder::find($request->combined_order_id);
            return $this->initialize($payment_type, $combined_order->id, $combined_order->grand_total, $user_id);
        } elseif ($payment_type == 'order_re_payment') {
            $order = Order::findOrFail($request->order_id);
            return $this->initialize($payment_type, $order->id, $order->grand_total, $user_id);
        } elseif ($payment_type == 'wallet_payment') {
            $id = 0;
            return $this->initialize($payment_type, $id, $request->amount, $user_id);
        } elseif (
            $payment_type == 'seller_package_payment' ||
            $payment_type == 'customer_package_payment'
        ) {
            return $this->initialize($payment_type, $request->package_id, $request->amount, $user_id);
        }
    }

    public function initialize($payment_type, $data, $amount, $user_id)
    {
        $user = User::find($user_id);
        //This generates a payment reference
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => $amount,
            'email' => $user->email,
            'tx_ref' => $reference,
            'currency' => env('FLW_PAYMENT_CURRENCY_CODE'),
            'redirect_url' => route(
                'api.flutterwave.callback',
                [
                    "payment_type" => $payment_type,
                    "data" => $data,  // $data = Combined Order Id / Order Id / Package Id
                    "amount" => $amount,
                    "user_id" => $user_id
                ]
            ),
            'customer' => [
                'email' => $user->email,
                "phone_number" => $user->phone,
                "name" => $user->name
            ],

            "customizations" => [
                "title" => 'Payment',
                "description" => ""
            ]
        ];

        $payment = Flutterwave::initializePayment($data);


        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return response()->json(['result' => false, 'url' => '', 'message' => "Could not find redirect url"]);
        }
        return response()->json(['result' => true, 'url' => $payment['data']['link'], 'message' => "Url generated"]);
    }



    public function callback(Request $request)
    {
        $status = $request->status;

        //if payment is successful
        if ($status ==  'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            try {
                $payment = $data['data'];

                if ($payment['status'] == "successful") {
                    if ($request->payment_type == 'cart_payment') {
                        checkout_done($request->data, json_encode($payment));
                    }
                    elseif ($request->payment_type == 'order_re_payment') {
                        order_re_payment_done($request->data, 'Flutterwave', json_encode($payment));
                    }
                    elseif ($request->payment_type == 'wallet_payment') {
                        wallet_payment_done($request->user_id, $request->amount, 'Flutterwave', json_encode($payment));
                    }
                    elseif ($request->payment_type == 'seller_package_payment') {
                        seller_purchase_payment_done($request->user_id, $request->data, 'Flutterwave', json_encode($payment));
                    }
                    elseif ($request->payment_type == 'customer_package_payment') {
                        customer_purchase_payment_done($request->user_id, $request->data, 'Flutterwave', json_encode($payment));
                    }

                    return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
                } else {
                    return response()->json(['result' => false, 'message' => translate("Payment is unsuccessful")]);
                }
            } catch (Exception $e) {
                return response()->json(['result' => false, 'message' => translate("Unsuccessful")]);
            }
        } elseif ($status ==  'cancelled') {
            return response()->json(['result' => false, 'message' => translate("Payment Cancelled")]);
        }
    }
}
