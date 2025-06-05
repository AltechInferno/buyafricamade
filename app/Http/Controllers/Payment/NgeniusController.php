<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CustomerPackage;
use App\Models\CombinedOrder;
use App\Models\Order;
use App\Utility\NgeniusUtility;
use Session;

class NgeniusController extends Controller
{
    public function pay()
    {
        $paymentType = Session::get('payment_type');
        $paymentData = Session::get('payment_data');
        if ($paymentType == 'cart_payment') {
            $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
            $amount = round($combined_order->grand_total * 100);
            //will be redirected
            NgeniusUtility::make_payment(route('ngenius.cart_payment_callback'), "cart_payment", $amount);
        } elseif ($paymentType == 'order_re_payment') {
            $order = Order::findOrFail($paymentData['order_id']);
            $amount = round($order->grand_total * 100);
            //will be redirected
            NgeniusUtility::make_payment(route('ngenius.order_re_payment_callback'), "order_re_payment", $amount);
        } elseif ($paymentType == 'wallet_payment') {
            $amount = round($paymentData['amount'] * 100);
            //will be redirected
            NgeniusUtility::make_payment(route('ngenius.wallet_payment_callback'), "wallet_payment", $amount);
        } elseif ($paymentType == 'customer_package_payment') {
            $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
            $amount = round($customer_package->amount * 100);
            //will be redirected
            NgeniusUtility::make_payment(route('ngenius.customer_package_payment_callback'), "customer_package_payment", $amount);
        } elseif ($paymentType == 'seller_package_payment') {
            $seller_package = \App\Models\SellerPackage::findOrFail($paymentData['seller_package_id']);
            $amount = round($seller_package->amount * 100);
            //will be redirected
            NgeniusUtility::make_payment(route('ngenius.seller_package_payment_callback'), "seller_package_payment", $amount);
        }


        $seller_package_id = $paymentData['seller_package_id'];
        $seller_package  = \App\Models\SellerPackage::findOrFail($seller_package_id);
    }

    public function cart_payment_callback()
    {
        if (request()->has('ref')) {
            return NgeniusUtility::check_callback(request()->get('ref'), "cart_payment");
        }
    }
    public function order_re_payment_callback()
    {
        if (request()->has('ref')) {
            return NgeniusUtility::check_callback(request()->get('ref'), "order_re_payment");
        }
    }
    public function wallet_payment_callback()
    {
        if (request()->has('ref')) {
            return NgeniusUtility::check_callback(request()->get('ref'), "wallet_payment");
        }
    }

    public function customer_package_payment_callback()
    {
        if (request()->has('ref')) {
            return NgeniusUtility::check_callback(request()->get('ref'), "customer_package_payment");
        }
    }

    public function seller_package_payment_callback()
    {
        if (request()->has('ref')) {
            return NgeniusUtility::check_callback(request()->get('ref'), "seller_package_payment");
        }
    }
}
