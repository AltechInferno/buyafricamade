<?php


namespace App\Http\Controllers\Api\V2;

use App\Models\CombinedOrder;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\This;

class BkashController extends Controller
{
    private $base_url;
    public function __construct()
    {
        if (get_setting('bkash_sandbox', 1)) {
            $this->base_url = "https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/";
        } else {
            $this->base_url = "https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/";
        }
    }

    public function begin(Request $request)
    {
        $payment_type = $request->payment_type;
        $combined_order_id = $request->combined_order_id;
        $amount = $request->amount;
        $user_id = $request->user_id;

        try {
            $token = $this->getToken();

            if ($payment_type == 'cart_payment') {
                $combined_order = CombinedOrder::find($combined_order_id);
                $amount = $combined_order->grand_total;
                $payerReference =  $payment_type . '-' . $combined_order->id . '-' . $user_id;
            }
            elseif ($payment_type == 'order_re_payment'){
                $order = Order::findOrFail($request->order_id);
                $amount = $order->grand_total;
                $payerReference =  $payment_type . '-' . $order->id . '-' . $user_id;
            }
            elseif ($payment_type == 'wallet_payment') {
                $amount = $request->amount;
                $payerReference = $payment_type . '-' . $amount . '-' . $user_id;
            }elseif ($payment_type == 'customer_package_payment' || $payment_type == 'seller_package_payment') {
                $payerReference =  $payment_type . '-' . $request->package_id . '-' . $user_id;
            }

            $requestbody = array(
                'mode' => '0011',
                'payerReference' => $payerReference,
                'callbackURL' => route('api.bkash.callback'),
                'amount' =>$amount,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => "Inv" . Date('YmdH') . rand(1000, 10000),
            );
            $requestbodyJson = json_encode($requestbody);
    
            $header = array(
                'Content-Type:application/json',
                'Authorization:' . $token ,
                'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
            );
    
            $url = curl_init($this->base_url . 'checkout/create');
            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
            $resultdata = curl_exec($url);
            
            curl_close($url);
    
            return redirect(json_decode($resultdata)->bkashURL);

        } catch (\Exception $exception) {
            return response()->json([
                'token' => '',
                'result' => false,
                'url' => '',
                'message' => $exception->getMessage()
            ]);
        }
    }

    public function callback(Request $request)
    {

        $allRequest = $request->all();
        if (isset($allRequest['status']) && $allRequest['status'] == 'success') {
            $resultdata = $this->execute($allRequest['paymentID']);

            if (!$resultdata) {
                $resultdata = $this->query($allRequest['paymentID']);
            }
            $response = json_decode($resultdata, true);
            $payerReference = explode("-", $response['payerReference']);

            if (isset($response['statusCode']) && $response['statusCode'] == "0000" && $response['transactionStatus'] == "Completed") {
                $payment_type = $payerReference[0];

                if ($payment_type == 'cart_payment') {
                    checkout_done($payerReference[1], json_encode($response));
                } elseif ($request->payment_type == 'order_re_payment') {
                    order_re_payment_done($payerReference[1], 'Bkash', json_encode($response));
                } elseif ($payment_type == 'wallet_payment') {
                    wallet_payment_done($payerReference[2], $payerReference[1], 'Bkash', json_encode($response));
                } elseif ($payment_type == 'customer_package_payment') {
                    customer_purchase_payment_done($payerReference[2], $payerReference[1], 'Bkash', json_encode($response));
                } elseif ($payment_type == 'seller_package_payment') {
                    seller_purchase_payment_done($payerReference[2], $payerReference[1], 'Bkash', json_encode($response));
                }

                return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
            } else {
                self::fail($allRequest);
            }
        } else {
            self::fail($allRequest);
        }
    }

    public static function fail($payment_details)
    {
        return response()->json([
            'result' => false,
            'message' => translate('Payment Failed'),
            'payment_details' => $payment_details
        ]);
    }


    public function getToken()
    {
        $request_data = array('app_key' => env('BKASH_CHECKOUT_APP_KEY'), 'app_secret' => env('BKASH_CHECKOUT_APP_SECRET'));
        $request_data_json = json_encode($request_data);

        $header = array(
            'Content-Type:application/json',
            'username:' . env('BKASH_CHECKOUT_USER_NAME'),
            'password:' . env('BKASH_CHECKOUT_PASSWORD')
        );

        $url = curl_init($this->base_url . 'checkout/token/grant');
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        curl_close($url);

        $token = json_decode($resultdata)->id_token;
        return $token;
    }


    public function execute($paymentID)
    {

        $auth = $this->getToken();

        $requestbody = array(
            'paymentID' => $paymentID
        );
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        $url = curl_init($this->base_url . 'checkout/execute');
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }


    public function query($paymentID)
    {

        $auth = $this->getToken();

        $requestbody = array(
            'paymentID' => $paymentID
        );
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        $url = curl_init($this->base_url . 'checkout/payment/status');
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }
}
