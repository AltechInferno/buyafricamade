<?php

namespace App\Http\Controllers\Payment;

use Auth;
use Session;
use Paystack;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\SellerPackage;
use App\Models\CustomerPackage;
use App\Http\Controllers\Controller;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\CustomerPackageController;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;



class PaystackController extends Controller
{

    public function generateToken()
    {
        $url = 'http://api.clicknship.com.ng/Token';
        $data = [
            'username' => 'cnsdemoapiacct',
            'password' => 'ClickNShip$12345',
            'grant_type' => 'password'
        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->post($url, [
            'form_params' => $data,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function pay(Request $request)
    {
        $post_data = array();
        $post_data['payment_type'] = Session::get('payment_type');

        $user = Auth::user();
        $currency = env('PAYSTACK_CURRENCY_CODE', 'NGN');
        $paymentData = Session::get('payment_data');
        $paymentType = Session::get('payment_type');


        // Get all session data
        $sessionData = Session::get('user_carts');

        // Log the session data
        Log::info('All Session Data:', $sessionData);

        Log::info('Payment Data in Pay Function:', ['paymentDatga' => $paymentData]);
        Log::info('Payment Type in Pay Function:', ['paymentType' => $paymentType]);
        Log::info('Post Data:', ['paymentType' => $post_data]);

        if (Session::get('payment_type') == 'cart_payment') {
            $post_data['combined_order_id'] = Session::get('combined_order_id');
            $array = ['custom_fields' => $post_data];

            $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));

            $request->email = $user->email;
            $request->amount = round($combined_order->grand_total * 100);
            $request->currency = $currency;
            $request->metadata = json_encode($array);
            $request->reference = Paystack::genTranxRef();

            return Paystack::getAuthorizationUrl()->redirectNow();

        } elseif (Session::get('payment_type') == 'order_re_payment') {
            $post_data['payment_method'] = $paymentData['payment_method'];
            $post_data['order_id'] = $paymentData['order_id'];
            $array = ['custom_fields' => $post_data];

            $order = Order::findOrFail($paymentData['order_id']);

            $request->email = $user->email;
            $request->amount = round($order->grand_total * 100);
            $request->currency = $currency;
            $request->metadata = json_encode($array);
            $request->reference = Paystack::genTranxRef();
            return Paystack::getAuthorizationUrl()->redirectNow();
        } elseif (Session::get('payment_type') == 'wallet_payment') {
            $post_data['payment_method'] = $paymentData['payment_method'];
            $array = ['custom_fields' => $post_data];

            $request->email = $user->email;
            $request->amount = round($paymentData['amount'] * 100);
            $request->currency = $currency;
            $request->metadata = json_encode($array);
            $request->reference = Paystack::genTranxRef();
            return Paystack::getAuthorizationUrl()->redirectNow();
        } elseif (Session::get('payment_type') == 'customer_package_payment') {
            $post_data['customer_package_id'] = $paymentData['customer_package_id'];
            $post_data['payment_method'] = $paymentData['payment_method'];
            $array = ['custom_fields' => $post_data];

            $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);

            $request->email = $user->email;
            $request->amount = round($customer_package->amount * 100);
            $request->currency = $currency;
            $request->metadata = json_encode($array);
            $request->reference = Paystack::genTranxRef();
            return Paystack::getAuthorizationUrl()->redirectNow();
        } elseif (Session::get('payment_type') == 'seller_package_payment') {
            $post_data['seller_package_id'] = $paymentData['seller_package_id'];
            $post_data['payment_method'] = $paymentData['payment_method'];
            $array = ['custom_fields' => $post_data];

            $seller_package = SellerPackage::findOrFail($paymentData['seller_package_id']);
            $user = Auth::user();
            $request->email = $user->email;
            $request->amount = round($seller_package->amount * 100);
            $request->currency = $currency;
            $request->metadata = json_encode($array);
            $request->reference = Paystack::genTranxRef();
            return Paystack::getAuthorizationUrl()->redirectNow();
        }
    }

    public function paystackNewCallback()
    {
        Paystack::getCallbackData();
    }




    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
        $payment = Paystack::getPaymentData();

        Log::info('Payment:', ['payment' => $payment]);

        if ($payment['data']['metadata'] && $payment['data']['metadata']['custom_fields']) {

            $payment_type = $payment['data']['metadata']['custom_fields']['payment_type'];

            // Log the payment and payment type
            Log::info('Payment Data:', ['payment' => $payment]);
            Log::info('Payment Type:', ['payment_type' => $payment_type]);

            if ($payment_type == 'cart_payment') {
                $payment_detalis = json_encode($payment);

                if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                    // Extract data from session
                    $tokenData = $this->generateToken();
                    $token = $tokenData['access_token'];
                    $carts = Session::get('user_carts');

                    if (!empty($carts) && isset($carts[0])) {
                            $cart = $carts[0]; // Assuming you want to use the first cart item for this example

                        // Prepare the request body
                        $requestBody = [
                            "OrderNo" =>     "Ord-" . $payment['data']['reference'],
                            "Description" => "PAIR OF SHOES AND BAGS",
                            "Weight" => 4.0, // Example weight, adjust as needed
                            "SenderName" => $cart['seller_details']['SenderName'],
                            "SenderCity" => $cart['seller_details']['SenderCity'],
                            "SenderTownID" => $cart['seller_details']['SenderTownID'], // Assuming city ID is used
                            "SenderAddress" => $cart['seller_details']['SenderAddress'],
                            "SenderPhone" => $cart['seller_details']['SenderPhone'],
                            "SenderEmail" => $cart['seller_details']['SenderEmail'],

                            "RecipientName" => $cart['user_details']['RecipientName'],
                            "RecipientCity" => $cart['user_details']['RecipientCity'], // Assuming city is available
                            "RecipientTownID" => $cart['user_details']['RecipientTownID'], // Assuming city ID is used
                            "RecipientAddress" => $cart['user_details']['RecipientAddress'], // Assuming address is available
                            "RecipientPhone" => $cart['user_details']['RecipientPhone'], // Assuming phone is available
                            "RecipientEmail" => $cart['user_details']['RecipientEmail'],
                            "PaymentType" => "Pay On Delivery",
                            "DeliveryType" => "Normal Delivery",
                            "PickupType" => "1",
                            "ShipmentItems" => [
                                [
                                    "ItemName" => $cart['ShipmentItems']['ItemName'],
                                    "ItemUnitCost" => $cart['ShipmentItems']['ItemUnitCost'],
                                    "ItemColour" => "BLACK", // Example color, adjust as needed
                                    "ItemSize" => "45" // Example size, adjust as needed
                                ]
                            ]
                        ];

                            // Make the POST request
                        $response = Http::withToken($token)
                                ->withHeaders(['Content-Type' => 'application/json'])
                                ->post('https://api.clicknship.com.ng/clicknship/Operations/PickupRequest', $requestBody);

                            // Handle the response
                            if ($response->successful()) {
                                Session::forget('user_carts');
                                Log::info('Pickup request successful:', $response->json());
                            } else {
                                Log::error('Pickup request failed:', $response->json());
                            }
                        } else {
                            Log::error('No cart items found in session.');
                        }

                    Auth::login(User::where('email', $payment['data']['customer']['email'])->first());
                    return (new CheckoutController)->checkout_done($payment['data']['metadata']['custom_fields']['combined_order_id'], $payment_detalis);
                }
                Session::forget('combined_order_id');
                flash(translate('Payment cancelled'))->success();
                return redirect()->route('home');

            } elseif ($payment_type == 'order_re_payment') {
                $payment_detalis = json_encode($payment);
                if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                    $payment_data['order_id'] = $payment['data']['metadata']['custom_fields']['order_id'];
                    $payment_data['payment_method'] = $payment['data']['metadata']['custom_fields']['payment_method'];
                    Auth::login(User::where('email', $payment['data']['customer']['email'])->first());
                    return (new CheckoutController)->orderRePaymentDone($payment_data, $payment);
                }
                Session::forget('payment_data');
                flash(translate('Payment cancelled'))->success();
                return redirect()->route('home');
            } elseif ($payment_type == 'wallet_payment') {
                $payment_detalis = json_encode($payment);
                if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                    $payment_data['amount'] = $payment['data']['amount'] / 100;
                    $payment_data['payment_method'] = $payment['data']['metadata']['custom_fields']['payment_method'];
                    Auth::login(User::where('email', $payment['data']['customer']['email'])->first());
                    return (new WalletController)->wallet_payment_done($payment_data, $payment_detalis);
                }
                Session::forget('payment_data');
                flash(translate('Payment cancelled'))->success();
                return redirect()->route('home');
            } elseif ($payment_type == 'customer_package_payment') {
                $payment_detalis = json_encode($payment);
                if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                    $payment_data['customer_package_id'] = $payment['data']['metadata']['custom_fields']['customer_package_id'];
                    $payment_data['payment_method'] = $payment['data']['metadata']['custom_fields']['payment_method'];
                    Auth::login(User::where('email', $payment['data']['customer']['email'])->first());
                    return (new CustomerPackageController)->purchase_payment_done($payment_data, $payment);
                }
                Session::forget('payment_data');
                flash(translate('Payment cancelled'))->success();
                return redirect()->route('home');
            } elseif ($payment_type == 'seller_package_payment') {
                $payment_detalis = json_encode($payment);
                if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                    $payment_data['seller_package_id'] = $payment['data']['metadata']['custom_fields']['seller_package_id'];
                    $payment_data['payment_method'] = $payment['data']['metadata']['custom_fields']['payment_method'];
                    Auth::login(User::where('email', $payment['data']['customer']['email'])->first());
                    return (new SellerPackageController)->purchase_payment_done($payment_data, $payment_detalis);
                }
                Session::forget('payment_data');
                flash(translate('Payment cancelled'))->success();
                return redirect()->route('home');
            }
        }
        // for mobile app
        else {
            if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                return response()->json(['result' => true, 'message' => "Payment is successful", 'payment_details' => $payment]);
            } else {
                return response()->json(['result' => false, 'message' => "Payment unsuccessful", 'payment_details' => $payment]);
            }
        }
    }
}
