<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\CheckoutController;
use MyFatoorah\Library\PaymentMyfatoorahApiV2;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SellerPackageController;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\Order;
use App\Models\SellerPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Session;
use Redirect;

class MyfatoorahController extends Controller
{
    public $mfObj;
    /**
     * create MyFatoorah object
     */
    public function __construct()
    {

        // If you want to set the credentials and the mode manually.
        $this->mfObj = new PaymentMyfatoorahApiV2(env('MYFATOORAH_TOKEN'), env('MYFATOORAH_COUNTRY_ISO'), get_setting('myfatoorah_sandbox') == 1 ? true : false);
    }

    /**
     * Create MyFatoorah invoice 
     *
     * @return \Illuminate\Http\Response
     */

    public function pay(Request $request)
    {
        $payment_type = $request->payment_type;
        $amount = $request->amount;
        $user = User::find($request->user_id);

        if ($payment_type == 'cart_payment') {
            $combined_order = CombinedOrder::findOrFail($request->combined_order_id);
            $amount = $combined_order->grand_total;
            $CustomerReference =  $payment_type . '-' . $combined_order->id . '-' . $user->id;
        } elseif ($payment_type == 'order_re_payment') {
            $order = Order::findOrFail($request->order_id);
            $amount = $order->grand_total;
            $CustomerReference =  $payment_type . '-' . $order->id . '-' . $user->id;
        } elseif ($payment_type == 'wallet_payment') {
            $CustomerReference = $payment_type . '-' . $amount . '-' . $user->id;
        } elseif ($payment_type == 'customer_package_payment' || $payment_type == 'seller_package_payment') {
            $CustomerReference =  $payment_type . '-' . $request->package_id . '-' . $user->id;
        }
        //
        $currency_code = \App\Models\Currency::find(get_setting('system_default_currency'))->code;
        $paymentMethodId = 0;
        $callbackURL = route('api.myfatoorah.callback');
        $data = [
            'InvoiceValue'       => $amount,
            'DisplayCurrencyIso' => $currency_code,
            'CallBackUrl'        => $callbackURL,
            'ErrorUrl'           => $callbackURL,
            'paymentMethodId'    => $paymentMethodId,
            'CustomerName'       => $user->name,
            'CustomerEmail'      => $user->email ?? 'test@test.com',
            'MobileCountryCode'  => '+965',
            'CustomerMobile'     => '12345678',
            'Language'           => 'en',
            'CustomerReference'  => $CustomerReference,
        ];
        try {
            $data            = $this->mfObj->getInvoiceURL($data, $paymentMethodId);
            if ($data['invoiceId']) {
                $checkoutUrl = $data['invoiceURL'];
                return Redirect::to($checkoutUrl);
            }
            return response()->json(['result' => false, 'message' => translate("Payment failed or got cancelled")]);
        } catch (\Exception $e) {
            // return  response()->json(['IsSuccess' => 'false', 'Message' => $e->getMessage()]);
            return response()->json(['result' => false, 'message' => translate("Payment failed or got cancelled")]);
        }
    }

    /**
     * Get MyFatoorah payment information
     * 
     * @return \Illuminate\Http\Response
     */

    public function callback(Request $request)
    {
        try {
            $response = $this->mfObj->getPaymentStatus(request('paymentId'), 'PaymentId');
            if ($response->InvoiceStatus == 'Paid') {
                $customerReference = explode("-", $response->CustomerReference);
                $payment_type = $customerReference[0];

                if ($payment_type == 'cart_payment') {
                    checkout_done($customerReference[1], json_encode($response));
                } elseif ($request->payment_type == 'order_re_payment') {
                    order_re_payment_done($customerReference[1], 'My Fatoorah', json_encode($response));
                } elseif ($payment_type == 'wallet_payment') {
                    wallet_payment_done($customerReference[2], $customerReference[1], 'My Fatoorah', json_encode($response));
                } elseif ($payment_type == 'customer_package_payment') {
                    customer_purchase_payment_done($customerReference[2], $customerReference[1], 'My Fatoorah', json_encode($response));
                } elseif ($payment_type == 'seller_package_payment') {
                    seller_purchase_payment_done($customerReference[2], $customerReference[1], 'My Fatoorah', json_encode($response));
                }

                return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
            } else {
                return response()->json(['result' => false, 'message' => translate("Payment failed or got cancelled")]);
            }
        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => translate("Payment failed or got cancelled")]);
        }
    }
}
