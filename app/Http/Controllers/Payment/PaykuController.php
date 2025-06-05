<?php

namespace App\Http\Controllers\Payment;

use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SebaCarrasco93\LaravelPayku\Facades\LaravelPayku;
use SebaCarrasco93\LaravelPayku\Models\PaykuTransaction;
use Session;
use Auth;

class PaykuController
{
    public function pay(Request $request)
    {
        if ($request->session()->has('payment_type')) {
            $paymentType = $request->session()->get('payment_type');
            $paymentData = Session::get('payment_data');

            $orderCode = rand(0000000, 11111111) . date('is');
            $email = Auth::user()->email;

            if ($paymentType == 'cart_payment') {
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $data = [
                    'order' => $orderCode,
                    'subject' => 'Cart Payment',
                    'amount' => $combined_order->grand_total,
                    'email' => $email
                ];
            } elseif ($paymentType == 'order_re_payment') {
                $order = Order::findOrFail($paymentData['order_id']);
                $data = [
                    'order' => $orderCode,
                    'subject' => 'Order Re Payment',
                    'amount' => $order->grand_total,
                    'email' => $email
                ];
            } elseif ($paymentType == 'wallet_payment') {
                $data = [
                    'order' => $orderCode,
                    'subject' => 'Wallet Payment',
                    'amount' => $paymentData['amount'],
                    'email' => $email
                ];
            } elseif ($paymentType == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
                $data = [
                    'order' => $orderCode,
                    'subject' => 'CustomerPackage Payment',
                    'amount' => $customer_package->amount,
                    'email' => $email
                ];
            } elseif ($paymentType == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail($paymentData['seller_package_id']);
                $data = [
                    'order' => $orderCode,
                    'subject' => 'SellerPackage Payment',
                    'amount' => $seller_package->amount,
                    'email' => $email
                ];
            }
        }

        return LaravelPayku::create($data['order'], $data['subject'], $data['amount'], $data['email']);
    }

    public function return($order)
    {
        $detail = LaravelPayku::return($order);

        return $detail;
    }

    public function notify($order)
    {
        $result = LaravelPayku::notify($order);
        $routeName = config('laravel-payku.route_finish_name');

        $routeExists = Route::has($routeName);

        if ($routeExists) {
            return redirect()->route($routeName, $result);
        }

        return view('payku::notify.missing-route', compact('result', 'routeName'));
    }

    public function callback($id)
    {
        $paykuTransaction = PaykuTransaction::find($id);

        if ($paykuTransaction->status == 'success') {
            $payment_type = Session::get('payment_type');
            $paymentData = session()->get('payment_data');

            if ($payment_type == 'cart_payment') {
                return (new CheckoutController)->checkout_done(session()->get('combined_order_id'), $paykuTransaction->toJson());
            }
            elseif ($payment_type == 'order_re_payment') {
                return (new CheckoutController)->orderRePaymentDone($paymentData, $paykuTransaction->toJson());
            }
            elseif ($payment_type == 'wallet_payment') {
                return (new WalletController)->wallet_payment_done($paymentData, $paykuTransaction->toJson());
            }
            elseif ($payment_type == 'customer_package_payment') {
                return (new CustomerPackageController)->purchase_payment_done($paymentData, $paykuTransaction->toJson());
            }
            elseif ($payment_type == 'seller_package_payment') {
                return (new SellerPackageController)->purchase_payment_done($paymentData, $paykuTransaction->toJson());
            }
        } else {
            flash(translate('Payment failed'))->error();
            return redirect()->route('home');
        }
    }
}
