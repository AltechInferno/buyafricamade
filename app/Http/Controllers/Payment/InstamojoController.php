<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\BusinessSetting;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CheckoutController;
use App\Models\Order;
use Session;
use Auth;

class InstamojoController extends Controller
{
    public function pay()
    {
        if (Session::has('payment_type')) {
            $user = Auth::user();
            $paymentType = Session::get('payment_type');
            $paymentData = Session::get('payment_data');

            $endPoint = get_setting('instamojo_sandbox') == 1 ? 'https://test.instamojo.com/api/1.1/' : 'https://www.instamojo.com/api/1.1/'; 

            $api = new \Instamojo\Instamojo(
                env('IM_API_KEY'),
                env('IM_AUTH_TOKEN'),
                $endPoint
            );

            if ($paymentType == 'cart_payment') {
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));

                if (preg_match_all('/^(?:(?:\+|0{0,2})91(\s*[\ -]\s*)?|[0]?)?[789]\d{9}|(\d[ -]?){10}\d$/im', $user->phone)) {
                    try {
                        $response = $api->paymentRequestCreate(array(
                            "purpose" => ucfirst(str_replace('_', ' ', $paymentType)),
                            "amount" => round($combined_order->grand_total),
                            "send_email" => false,
                            "email" => $user->email,
                            "phone" => $user->phone,
                            "redirect_url" => url('instamojo/payment/pay-success')
                        ));
                        return redirect($response['longurl']);
                    } catch (\Exception $e) {
                        print('Error: ' . $e->getMessage());
                    }
                } else {
                    flash(translate('Please add phone number to your profile'))->warning();
                    return redirect()->route('profile');
                }
            }
            elseif ($paymentType == 'order_re_payment') {
                $order = Order::findOrFail($paymentData['order_id']);

                if (preg_match_all('/^(?:(?:\+|0{0,2})91(\s*[\ -]\s*)?|[0]?)?[789]\d{9}|(\d[ -]?){10}\d$/im', $user->phone)) {
                    try {
                        $response = $api->paymentRequestCreate(array(
                            "purpose" => ucfirst(str_replace('_', ' ', $paymentType)),
                            "amount" => round($order->grand_total),
                            "send_email" => false,
                            "email" => $user->email,
                            "phone" => $user->phone,
                            "redirect_url" => url('instamojo/payment/pay-success')
                        ));
                        return redirect($response['longurl']);
                    } catch (\Exception $e) {
                        print('Error: ' . $e->getMessage());
                    }
                } else {
                    flash(translate('Please add phone number to your profile'))->warning();
                    return redirect()->route('profile');
                }
            }
            elseif ($paymentType == 'wallet_payment') {
                if (preg_match_all('/^(?:(?:\+|0{0,2})91(\s*[\ -]\s*)?|[0]?)?[789]\d{9}|(\d[ -]?){10}\d$/im', $user->phone)) {
                    try {

                        $response = $api->paymentRequestCreate(array(
                            "purpose" => ucfirst(str_replace('_', ' ', $paymentType)),
                            "amount" => round($paymentData['amount']),
                            "send_email" => false,
                            "email" => $user->email,
                            "phone" => $user->phone,
                            "redirect_url" => url('instamojo/payment/pay-success')
                        ));
                        return redirect($response['longurl']);
                        // dd($response);
                    } catch (\Exception $e) {
                        return back();
                    }
                } else {
                    flash(translate('Please add phone number to your profile'))->warning();
                    return redirect()->route('profile');
                }
            }
            elseif ($paymentType == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
                if (preg_match_all('/^(?:(?:\+|0{0,2})91(\s*[\ -]\s*)?|[0]?)?[789]\d{9}|(\d[ -]?){10}\d$/im', $user->phone)) {
                    try {
                        $response = $api->paymentRequestCreate(array(
                            "purpose" => ucfirst(str_replace('_', ' ', $paymentType)),
                            "amount" => round($customer_package->amount),
                            "send_email" => false,
                            "email" => $user->email,
                            "phone" => $user->phone,
                            "redirect_url" => url('instamojo/payment/pay-success')
                        ));

                        return redirect($response['longurl']);
                    } catch (\Exception $e) {
                        return back();
                    }
                } else {
                    flash(translate('Please add phone number to your profile'))->warning();
                    return redirect()->route('profile');
                }
            } elseif ($paymentType == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail($paymentData['seller_package_id']);
                if (preg_match_all('/^(?:(?:\+|0{0,2})91(\s*[\ -]\s*)?|[0]?)?[789]\d{9}|(\d[ -]?){10}\d$/im', $user->phone)) {
                    try {
                        $response = $api->paymentRequestCreate(array(
                            "purpose" => ucfirst(str_replace('_', ' ', $paymentType)),
                            "amount" => round($seller_package->amount),
                            "send_email" => false,
                            "email" => $user->email,
                            "phone" => $user->phone,
                            "redirect_url" => url('instamojo/payment/pay-success')
                        ));

                        return redirect($response['longurl']);
                    } catch (\Exception $e) {
                        return back();
                    }
                } else {
                    flash(translate('Please add phone number to your profile'))->warning();
                    return redirect()->route('profile');
                }
            }
        }
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

            if (!isset($response['payments'][0]['status'])) {
                flash(translate('Payment Failed'))->error();
                return redirect()->route('home');
            } else if ($response['payments'][0]['status'] != 'Credit') {
                flash(translate('Payment Failed'))->error();
                return redirect()->route('home');
            }
        } catch (\Exception $e) {
            flash(translate('Payment Failed'))->error();
            return redirect()->route('home');
        }

        $payment = json_encode($response);

        if (Session::has('payment_type')) {
            $paymentType = Session::get('payment_type');
            $paymentData = $request->session()->get('payment_data');
            if ($paymentType == 'cart_payment') {
                return (new CheckoutController)->checkout_done(Session::get('combined_order_id'), $payment);
            } elseif ($paymentType == 'order_re_payment') {
                return (new CheckoutController)->orderRePaymentDone($paymentData, $payment);
            } elseif ($paymentType == 'wallet_payment') {
                return (new WalletController)->wallet_payment_done($paymentData, $payment);
            } elseif ($paymentType == 'customer_package_payment') {
                return (new CustomerPackageController)->purchase_payment_done($paymentData, $payment);
            } elseif ($paymentType == 'seller_package_payment') {
                return (new SellerPackageController)->purchase_payment_done($paymentData, $payment);
            }
        }
    }
}
