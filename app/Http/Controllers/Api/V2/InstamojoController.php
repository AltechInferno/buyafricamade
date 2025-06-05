<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\Order;

class InstamojoController extends Controller
{
    public function pay(Request $request)
    {   
        $endPoint = get_setting('instamojo_sandbox') == 1 ? 'https://test.instamojo.com/api/1.1/' : 'https://www.instamojo.com/api/1.1/';

        $api = new \Instamojo\Instamojo(
            env('IM_API_KEY'),
            env('IM_AUTH_TOKEN'),
            $endPoint
        );
        $user = auth()->user();

        if (preg_match_all('/^(?:(?:\+|0{0,2})91(\s*[\ -]\s*)?|[0]?)?[789]\d{9}|(\d[ -]?){10}\d$/im', $user->phone)) {

            $paymentType = $request->payment_type;
            $amount      = round($request->amount);
            
            if ($paymentType == 'cart_payment') {
                $combined_order = CombinedOrder::findOrFail($request->combined_order_id);
                $amount =  round($combined_order->grand_total);
                $orderID = $combined_order->id;
            }
            elseif ($paymentType == 'order_re_payment') {
                $order = Order::findOrFail($request->order_id);
                $amount =  round($order->grand_total);
                $orderID = $order->id;
            }

            try {
                $response = $api->paymentRequestCreate(array(
                    "purpose" => ucfirst(str_replace('_', ' ', $paymentType)),
                    "amount" => $amount,
                    "send_email" => false,
                    "email" => $user->email,
                    "phone" => $user->phone,
                    "redirect_url" => url("api/v2/instamojo/success?payment_option=$request->payment_option&payment_type=$paymentType&order_id=$orderID&amount=$amount&package_id=$request->package_id")
                ));
                return redirect($response['longurl']);
            } catch (\Exception $e) {
            }
        }
        return redirect(url("api/v2/online-pay/failed"))->with("errors",'Please add phone number to your profile');
    }
    // success response method.
    public function success(Request $request)
    {
        try {
            $endPoint = get_setting('instamojo_sandbox') == 1 ? 'https://test.instamojo.com/api/1.1/' : 'https://www.instamojo.com/api/1.1/';
            $api = new \Instamojo\Instamojo(
                env('IM_API_KEY'),
                env('IM_AUTH_TOKEN'),
                $endPoint
            );

            $response = $api->paymentRequestStatus(request('payment_request_id'));

            if (!isset($response['payments'][0]['status']) || $response['payments'][0]['status'] != 'Credit') {
                return redirect(url("api/v2/online-pay/failed"))->with("errors",translate('Payment Failed'));
            }
        } catch (\Exception $e) {
            return redirect(url("api/v2/online-pay/failed"))->with('errors',translate('Payment Failed'));
        }

        $payment = json_encode($response);
        return redirect( url("api/v2/online-pay/success?payment_type=$request->payment_type&order_id=$request->order_id&amount=$request->amount&package_id=$request->package_id&payment_details=$payment"));

    }
}
