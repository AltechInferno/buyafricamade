<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use App\Models\Order;
use Session;
use Auth;

class MercadopagoController extends Controller
{
    public function pay()
    {
        $amount = 0;
        if (Session::has('payment_type')) {
            $paymentType = Session::get('payment_type');
            $paymentData = Session::get('payment_data');
            
            $user = Auth::user();
            $first_name = $user->name;
            $phone = ($user->phone != null) ? $user->phone : '123456789';
            $email = ($user->email != null) ? $user->email : 'example@example.com';

            if ($paymentType == 'cart_payment') {
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $amount = round($combined_order->grand_total);
                $combined_order_id = $combined_order->id;
                $billname = 'Ecommerce Cart Payment';
                $first_name = json_decode($combined_order->shipping_address)->name;
                $phone = json_decode($combined_order->shipping_address)->phone;
                $email = json_decode($combined_order->shipping_address)->email;
            } elseif ($paymentType == 'order_re_payment') {
                $order = Order::findOrFail($paymentData['order_id']);
                $amount = round($order->grand_total);
                $combined_order_id = $order->combined_order_id;
                $billname = 'Order Re Payment';
                $first_name = json_decode($order->shipping_address)->name;
                $phone = json_decode($order->shipping_address)->phone;
                $email = json_decode($order->shipping_address)->email;
            } elseif ($paymentType == 'wallet_payment') {
                $amount = $paymentData['amount'];
                $combined_order_id = rand(10000, 99999);
                $billname = 'Wallet Payment';
            } elseif ($paymentType == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
                $amount = round($customer_package->amount);
                $combined_order_id = rand(10000, 99999);
                $billname = 'Customer Package Payment';
            } elseif ($paymentType == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail($paymentData['seller_package_id']);
                $amount = round($seller_package->amount);
                $combined_order_id = rand(10000, 99999);
                $billname = 'Seller Package Payment';
            }

            $success_url = url('/mercadopago/payment/done');
            $fail_url = url('/mercadopago/payment/cancel');
        }
        return view('frontend.payment.mercadopago', compact('combined_order_id', 'billname', 'phone', 'amount', 'first_name', 'email', 'success_url', 'fail_url'));
    }



    public function paymentstatus()
    {

        $response = request()->status;
        if ($response == 'approved') {
            $payment = ["status" => "Success"];
            $payment_type = Session::get('payment_type');
            $paymentData = session()->get('payment_data');

            if ($payment_type == 'cart_payment') {
                flash(translate("Your order has been placed successfully"))->success();
                return (new CheckoutController)->checkout_done(session()->get('combined_order_id'), json_encode($payment));
            } elseif ($payment_type == 'order_re_payment') {
                return (new CheckoutController)->orderRePaymentDone($paymentData, json_encode($payment));
            } elseif ($payment_type == 'wallet_payment') {
                return (new WalletController)->wallet_payment_done($paymentData, json_encode($payment));
            } elseif ($payment_type == 'customer_package_payment') {
                return (new CustomerPackageController)->purchase_payment_done($paymentData, json_encode($payment));
            } elseif ($payment_type == 'seller_package_payment') {
                return (new SellerPackageController)->purchase_payment_done($paymentData, json_encode($payment));
            }
        } else {
            flash(translate('Payment is cancelled'))->error();
            return redirect()->route('home');
        }
    }

    public function callback()
    {

        $response = request()->all(['collection_id', 'collection_status', 'payment_id', 'status', 'preference_id']);
        //Log::info($response);
        flash(translate('Payment is cancelled'))->error();
        return redirect()->route('home');
    }
}
