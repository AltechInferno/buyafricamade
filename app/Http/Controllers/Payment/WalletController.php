<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\Order;
use Session;
use Auth;

class WalletController extends Controller
{
    public function pay(){
        if(Session::has('payment_type')){
            $paymentType = Session::get('payment_type');
            $paymentData = Session::get('payment_data');
            $user = Auth::user();

            if($paymentType == 'cart_payment'){
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                if ($user->balance >= $combined_order->grand_total) {
                    $user->balance -= $combined_order->grand_total;
                    $user->save();
                    return (new CheckoutController)->checkout_done($combined_order->id, null);
                }
            }
            elseif($paymentType == 'order_re_payment'){
                $order = Order::findOrFail($paymentData['order_id']);
                if ($user->balance >= $order->grand_total) {
                    $user->balance -= $order->grand_total;
                    $user->save();
                    return (new CheckoutController)->orderRePaymentDone($paymentData);
                }
            }
            elseif ($paymentType == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
                $amount = $customer_package->amount;
                if ($user->balance >= $amount) {
                    $user->balance -= $amount;
                    $user->save();
                    return (new CustomerPackageController)->purchase_payment_done($paymentData);
                }
                flash(translate("You don't have enough wallet balance."))->error();
                return redirect()->route('customer_packages_list_show');
            }
        }
    }
}
