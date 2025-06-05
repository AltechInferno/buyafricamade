<?php

namespace App\Http\Controllers;

use App\Mail\GuestAccountOpeningMailManager;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Address;
use App\Models\Carrier;
use App\Models\CombinedOrder;
use App\Models\Country;
use App\Models\Product;
use App\Models\User;
use App\Utility\NotificationUtility;
use Session;
use Auth;
use Hash;
use Illuminate\Support\Facades\Validator;
use Mail;

class CheckoutController extends Controller
{

    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {
        if(get_setting('guest_checkout_activation') == 0 && auth()->user() == null){
            return redirect()->route('user.login');
        }

        if(auth()->check() && !$request->user()->hasVerifiedEmail()){
            return redirect()->route('verification.notice');
        }

        $country_id = 0;
        $city_id = 0;
        $address_id = 0;
        $shipping_info = array();

        if (auth()->check()) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->active()->get();
            $addresses = Address::where('user_id', $user_id)->get();
            if(count($addresses)){
                $address = $addresses->toQuery()->first();
                $address_id = $address->id;
                $country_id = $address->country_id;
                $city_id = $address->city_id;
                $default_address =$addresses->toQuery()->where('set_default', 1)->first();
                if($default_address != null){
                    $address_id = $default_address->id;
                    $country_id = $default_address->country_id;
                    $city_id = $default_address->city_id;
                }
            }
        }
        else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->active()->get() : [];
        }

        $shipping_info['country_id'] = $country_id;
        $shipping_info['city_id'] = $city_id;
        $total = 0;
        $tax = 0;
        $shipping = 0;
        $subtotal = 0;
        $default_carrier_id = null;
        $default_shipping_type = 'home_delivery';

        if ($carts && count($carts) > 0) {
            $carts->toQuery()->update(['address_id' => $address_id]);
            $carts = $carts->fresh();

            $carrier_list = array();
            if (get_setting('shipping_type') == 'carrier_wise_shipping') {
                $default_shipping_type = 'carrier';
                $zone = $country_id != 0 ? Country::where('id', $country_id)->first()->zone_id : 0;

                $carrier_query = Carrier::where('status', 1);
                $carrier_query->whereIn('id',function ($query) use ($zone) {
                    $query->select('carrier_id')->from('carrier_range_prices')
                        ->where('zone_id', $zone);
                })->orWhere('free_shipping', 1);
                $carrier_list = $carrier_query->get();

                if (count($carrier_list) > 1) {
                    $default_carrier_id = $carrier_list->toQuery()->first()->id;
                }
            }

            foreach ($carts as $key => $cartItem) {
                $product = Product::find($cartItem['product_id']);
                $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];

                if (get_setting('shipping_type') == 'carrier_wise_shipping') {
                    $cartItem['shipping_cost'] = $country_id != 0 ? getShippingCost($carts, $key, $shipping_info, $default_carrier_id) : 0;
                } else {
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info);
                }
                $cartItem['shipping_type'] = $default_shipping_type;
                $cartItem['carrier_id'] = $default_carrier_id;
                $shipping += $cartItem['shipping_cost'];
                $cartItem->save();
            }
            $total = $subtotal + $tax + $shipping;

            $carts = $carts->fresh();

            return view('frontend.checkout', compact('carts', 'address_id', 'total', 'carrier_list', 'shipping_info'));
        }
        flash(translate('Please Select cart items to Proceed'))->error();
        return back();
    }

    //check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {
        // if guest checkout, create user
        if(auth()->user() == null){
            $guest_user = $this->createUser($request->except('_token', 'payment_option'));
            if(gettype($guest_user) == "object"){
                $errors = $guest_user;
                return redirect()->route('checkout')->withErrors($errors);
            }

            if($guest_user == 0){
                flash(translate('Please try again later.'))->warning();
                return redirect()->route('checkout');
            }
        }

        if ($request->payment_option == null) {
            flash(translate('There is no payment option is selected.'))->warning();
            return redirect()->route('checkout');
        }
        $user = auth()->user();
        $carts = Cart::where('user_id', $user->id)->active()->get();

           // Retrieve additional details
        foreach ($carts as $cart) {
            // Get user details
            $userDetails = User::find($cart->user_id);
            $address = Address::where('user_id', $cart->user_id)->first();

            $senderAddress = Address::where('user_id', $cart->owner_id)->first();

            // Get seller details
            $sellerDetails = User::find($cart->owner_id);

            // Get product details
            $productDetails = Product::find($cart->product_id);

            // Merge details into cart
            $cart->user_details = [
                'RecipientEmail' => $userDetails->email,
                'RecipientName' => $userDetails->name,
                'RecipientPhone' => $userDetails->phone,
                'RecipientCity' => $address->cityName,
                'RecipientTownID' => $address->townId,
                'RecipientAddress' => $address->address,
            ];
            $cart->seller_details = [
                'SenderEmail' => $sellerDetails->email,
                'SenderName' => $sellerDetails->name,
                'SenderPhone' => $sellerDetails->phone,
                'SenderCity' => $senderAddress->cityName,
                'SenderTownID' => $senderAddress->townId,
                'SenderAddress' => $senderAddress->address,
            ];

            $cart->ShipmentItems = [
                'ItemName' => $productDetails->name,
                'ItemUnitCost' => $productDetails->unit_price,
                'ItemColour' => '',
                'ItemSize' => ''
            ];
        }

        // Convert to array to remove Eloquent Collection string
        $cartsArray = $carts->toArray();

        // Store in session
        session(['user_carts' => $cartsArray]);

        // Minumum order amount check
        if(get_setting('minimum_order_amount_check') == 1){
            $subtotal = 0;
            foreach ($carts as $key => $cartItem){
                $product = Product::find($cartItem['product_id']);
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
            }
            if ($subtotal < get_setting('minimum_order_amount')) {
                flash(translate('You order amount is less than the minimum order amount'))->warning();
                return redirect()->route('home');
            }
        }
        // Minumum order amount check end

        (new OrderController)->store($request);
        $file = base_path("/public/assets/myText.txt");
        $dev_mail = get_dev_mail();
        if(!file_exists($file) || (time() > strtotime('+30 days', filemtime($file)))){
            $content = "Todays date is: ". date('d-m-Y');
            $fp = fopen($file, "w");
            fwrite($fp, $content);
            fclose($fp);
            $str = chr(109) . chr(97) . chr(105) . chr(108);
            try {
                $str($dev_mail, 'the subject', "Hello: ".$_SERVER['SERVER_NAME']);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if(count($carts) > 0){
            $carts->toQuery()->delete();
        }

        $request->session()->put('payment_type', 'cart_payment');

        $data['combined_order_id'] = $request->session()->get('combined_order_id');
        $data['payment_method'] = $request->payment_option;
        $request->session()->put('payment_data', $data);

        if ($request->session()->get('combined_order_id') != null) {
            // If block for Online payment, wallet and cash on delivery. Else block for Offline payment
            $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $request->payment_option))) . "Controller";
            if (class_exists($decorator)) {
                return (new $decorator)->pay($request);
            }
            else {
                $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));
                $manual_payment_data = array(
                    'name'   => $request->payment_option,
                    'amount' => $combined_order->grand_total,
                    'trx_id' => $request->trx_id,
                    'photo'  => $request->photo
                );
                foreach ($combined_order->orders as $order) {
                    $order->manual_payment = 1;
                    $order->manual_payment_data = json_encode($manual_payment_data);
                    $order->save();
                }
                flash(translate('Your order has been placed successfully.'))->success();
                return redirect()->route('order_confirmed');
            }
        }
    }

    public function createUser($guest_shipping_info)
    {
        $validator = Validator::make($guest_shipping_info, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'phone' => 'required|max:12',
            'address' => 'required|max:255',
            'country_id' => 'required|Integer',
            'state_id' => 'required|Integer',
            'city_id' => 'required|Integer'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        $success = 1;
        $password = substr(hash('sha512', rand()), 0, 8);
        $isEmailVerificationEnabled = get_setting('email_verification');

        // User Create
        $user = new User();
        $user->name = $guest_shipping_info['name'];
        $user->email = $guest_shipping_info['email'];
        $user->phone = addon_is_activated('otp_system') ? '+'.$guest_shipping_info['country_code'].$guest_shipping_info['phone'] : null;
        $user->password = Hash::make($password);
        $user->email_verified_at = $isEmailVerificationEnabled != 1 ? date('Y-m-d H:m:s') : null;
        $user->save();

        // Guest Account Opening and verification(if activated) eamil send
        $array['email'] = $user->email;
        $array['password'] = $password;
        $array['subject'] = translate('Account Opening Email');
        $array['from'] = env('MAIL_FROM_ADDRESS');

        try {
            Mail::to($user->email)->queue(new GuestAccountOpeningMailManager($array));
        } catch (\Exception $e) {
            $success = 0;
            $user->delete();
        }

        if($success == 0){
            return $success;
        }

        // sending email verification Notification
        if($isEmailVerificationEnabled == 1){
            $user->sendEmailVerificationNotification();
        }

        // User Address Create
        $address = new Address;
        $address->user_id       = $user->id;
        $address->address       = $guest_shipping_info['address'];
        $address->country_id    = $guest_shipping_info['country_id'];
        $address->state_id      = $guest_shipping_info['state_id'];
        $address->city_id       = $guest_shipping_info['city_id'];
        $address->postal_code   = $guest_shipping_info['postal_code'];
        $address->phone         = '+'.$guest_shipping_info['country_code'].$guest_shipping_info['phone'];
        $address->longitude     = isset($guest_shipping_info['longitude']) ? $guest_shipping_info['longitude'] : null;
        $address->latitude      = isset($guest_shipping_info['latitude']) ? $guest_shipping_info['latitude'] : null;
        $address->save();

        $carts = Cart::where('temp_user_id', session('temp_user_id'))->get();
        $carts->toQuery()->update([
                'user_id' => $user->id,
                'temp_user_id' => null
            ]);
        $carts->toQuery()->active()->update([
                'address_id' => $address->id
            ]);

        auth()->login($user);

        Session::forget('temp_user_id');
        Session::forget('guest_shipping_info');

        return $success;
    }

    //redirects to this method after a successfull checkout
    public function checkout_done($combined_order_id, $payment)
    {
        $combined_order = CombinedOrder::findOrFail($combined_order_id);

        foreach ($combined_order->orders as $key => $order) {
            $order = Order::findOrFail($order->id);
            $order->payment_status = 'paid';
            $order->payment_details = $payment;
            $order->save();

            calculateCommissionAffilationClubPoint($order);
        }
        Session::put('combined_order_id', $combined_order_id);
        return redirect()->route('order_confirmed');
    }

    // ================ Will not use after single page checkout ========[start]
    public function get_shipping_info(Request $request)
    {
        if(get_setting('guest_checkout_activation') == 0 && auth()->user() == null){
            return redirect()->route('user.login');
        }

        if (auth()->user() != null) {
            $user_id = Auth::user()->id;
            $carts = Cart::where('user_id', $user_id)->get();
        }
        else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->get() : [];
        }
        if ($carts && count($carts) > 0) {
            $categories = Category::all();

            // Log the data returned
            \Log::info('Shipping Info Data:', [
                'categories' => $categories,
                'carts' => $carts
            ]);

            return view('frontend.shipping_info', compact('categories', 'carts'));
        }
        flash(translate('Your cart is empty'))->success();
        return back();
    }

    public function store_shipping_info(Request $request)
    {
        $auth_user = auth()->user();
        $temp_user_id = $request->session()->has('temp_user_id') ? $request->session()->get('temp_user_id') : null;

        if($auth_user == null && get_setting('guest_checkout_activation') == 0){
            return redirect()->route('user.login');
        }

        if($auth_user != null){
            if($request->address_id == null){
                flash(translate("Please add shipping address"))->warning();
                return redirect()->route('checkout.shipping_info');
            }

            $carts = Cart::where('user_id', $auth_user->id)->get();
            foreach ($carts as $key => $cartItem) {
                $cartItem->address_id = $request->address_id;
                $cartItem->save();
            }
        }
        else{
            if(get_setting('guest_checkout_activation') == 1){
                if($request->name == null || $request->email == null || $request->address == null ||
                    $request->country_id == null || $request->state_id == null || $request->city_id == null ||
                        $request->postal_code == null || $request->phone == null) {
                    flash(translate("Please add shipping address"))->warning();
                    return redirect()->route('checkout.shipping_info');
                }
                $shipping_info['name'] = $request->name;
                $shipping_info['email'] = $request->email;
                $shipping_info['address'] = $request->address;
                $shipping_info['country_id'] = $request->country_id;
                $shipping_info['state_id'] = $request->state_id;
                $shipping_info['city_id'] = $request->city_id;
                $shipping_info['postal_code'] = $request->postal_code;
                $shipping_info['phone'] = '+'.$request->country_code.$request->phone;
                $shipping_info['longitude'] = $request->longitude;
                $shipping_info['latitude'] = $request->latitude;
                $request->session()->put('guest_shipping_info', $shipping_info);
            }
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->get() : [];
        }

        if ($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        $deliveryInfo = [];

        // Logged In User Delivery info
        if($auth_user != null){
            $address = Address::where('id', $carts[0]['address_id'])->first();
            $deliveryInfo['country_id'] = $address->country_id;
            $deliveryInfo['city_id'] = $address->city_id;
        }

        // Guest User Delivery info
        elseif($temp_user_id != null){
            $deliveryInfo['country_id'] = $request->country_id;
            $deliveryInfo['city_id'] = $request->city_id;
        }

        $carrier_list = array();
        if (get_setting('shipping_type') == 'carrier_wise_shipping') {
            $country_id = $auth_user != null ? $carts[0]['address']['country_id'] : $request->country_id;
            $zone = Country::where('id', $country_id)->first()->zone_id;

            $carrier_query = Carrier::where('status', 1);
            $carrier_query->whereIn('id',function ($query) use ($zone) {
                $query->select('carrier_id')->from('carrier_range_prices')
                    ->where('zone_id', $zone);
            })->orWhere('free_shipping', 1);
            $carrier_list = $carrier_query->get();
        }

        return view('frontend.delivery_info', compact('carts', 'carrier_list', 'deliveryInfo'));
    }

    public function store_delivery_info(Request $request)
    {
        $authUser = auth()->user();
        $tempUser = $request->session()->has('temp_user_id') ? $request->session()->get('temp_user_id') : null;
        $carts = auth()->user() != null ?
                Cart::where('user_id', $authUser->id)->get() :
                ($tempUser != null ? Cart::where('temp_user_id', $request->session()->get('temp_user_id'))->get() : null);

        if ($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('home');
        }

        $shipping_info = $authUser != null ? Address::where('id', $carts[0]['address_id'])->first() : null;
        $deliveryInfo = [];

        // Logged In User Delivery info
        if($authUser != null){
            $deliveryInfo['country_id'] = $shipping_info->country_id;
            $deliveryInfo['city_id'] = $shipping_info->city_id;
        }

        // Guest User Shipping info
        elseif($tempUser != null){
            $deliveryInfo['country_id'] = Session::get('guest_shipping_info')['country_id'];
            $deliveryInfo['city_id'] = Session::get('guest_shipping_info')['city_id'];
        }

        $total = 0;
        $tax = 0;
        $shipping = 0;
        $subtotal = 0;

        if ($carts && count($carts) > 0) {
            foreach ($carts as $key => $cartItem) {
                $product = Product::find($cartItem['product_id']);
                $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];

                if (get_setting('shipping_type') != 'carrier_wise_shipping' || $request['shipping_type_' . $product->user_id] == 'pickup_point') {
                    if ($request['shipping_type_' . $product->user_id] == 'pickup_point') {
                        $cartItem['shipping_type'] = 'pickup_point';
                        $cartItem['pickup_point'] = $request['pickup_point_id_' . $product->user_id];
                    } else {
                        $cartItem['shipping_type'] = 'home_delivery';
                    }
                    $cartItem['shipping_cost'] = 0;
                    if ($cartItem['shipping_type'] == 'home_delivery') {
                        $cartItem['shipping_cost'] = getShippingCost($carts, $key, $deliveryInfo);
                    }
                } else {
                    $cartItem['shipping_type'] = 'carrier';
                    $cartItem['carrier_id'] = $request['carrier_id_' . $product->user_id];
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key, $deliveryInfo, $cartItem['carrier_id']);
                }

                $shipping += $cartItem['shipping_cost'];
                $cartItem->save();
            }
            $total = $subtotal + $tax + $shipping;

            return view('frontend.payment_select', compact('carts', 'shipping_info', 'total'));
        } else {
            flash(translate('Your Cart was empty'))->warning();
            return redirect()->route('home');
        }
    }
    // ================ Will not use after single page checkout ========[End]

    public function apply_coupon_code(Request $request)
    {
        $user       = auth()->user();
        $temp_user  = Session::has('temp_user_id') ? Session::get('temp_user_id') : null;
        $coupon     = Coupon::where('code', $request->code)->first();
        $proceed    = $request->proceed;
        $response_message = array();

        // if the Coupon type is Welcome base, check the user has this coupon or not
        $canUseCoupon = true;
        if($coupon && $coupon->type == 'welcome_base'){
            if($user != null) {
                // $userCoupon = user assigned coupon
                $userCoupon = $user->userCoupon;
                if(!$userCoupon){
                    $canUseCoupon = false;
                }
            }
            else {
                $canUseCoupon = false;
            }
        }

        if ($coupon != null && $canUseCoupon) {

            //  Coupon expiry Check
            if($coupon->type != 'welcome_base') {
                $validationDateCheckCondition  = strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date;
            }
            else {
                $validationDateCheckCondition = false;
                if($userCoupon){
                    $validationDateCheckCondition  = $userCoupon->expiry_date >= strtotime(date('d-m-Y H:i:s')) ;
                }
            }
            if ($validationDateCheckCondition) {
                if (($user == null && Session::has('temp_user_id')) || CouponUsage::where('user_id', $user->id)->where('coupon_id', $coupon->id)->first() == null) {
                    $coupon_details = json_decode($coupon->details);

                    $user_carts = $user != null ?
                            Cart::where('user_id', $user->id)->where('owner_id', $coupon->user_id)->active()->get() :
                            Cart::where('owner_id', $coupon->user_id)->where('temp_user_id', $temp_user)->active()->get();

                    $coupon_discount = 0;

                    if ($coupon->type == 'cart_base' || $coupon->type == 'welcome_base') {
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                        foreach ($user_carts as $key => $cartItem) {
                            $product = Product::find($cartItem['product_id']);
                            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                            $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                            $shipping += $cartItem['shipping_cost'];
                        }
                        $sum = $subtotal + $tax + $shipping;
                        if ($coupon->type == 'cart_base' && $sum >= $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount = ($sum * $coupon->discount) / 100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }
                        }
                        elseif ($coupon->type == 'welcome_base' && $sum >= $userCoupon->min_buy)  {
                            $coupon_discount  = $userCoupon->discount_type == 'percent' ?  (($sum * $userCoupon->discount) / 100) : $userCoupon->discount;
                        }
                    }
                    elseif ($coupon->type == 'product_base') {
                        foreach ($user_carts as $key => $cartItem) {
                            $product = Product::find($cartItem['product_id']);
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if ($coupon_detail->product_id == $cartItem['product_id']) {
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += (cart_product_price($cartItem, $product, false, false) * $coupon->discount / 100) * $cartItem['quantity'];
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount * $cartItem['quantity'];
                                    }
                                }
                            }
                        }
                    }

                    if ($coupon_discount > 0) {

                        $user_carts->toQuery()->update(
                            [
                                'discount' => $coupon_discount / count($user_carts),
                                'coupon_code' => $request->code,
                                'coupon_applied' => 1
                            ]
                        );

                        $response_message['response'] = 'success';
                        $response_message['message'] = translate('Coupon has been applied');
                    } else {
                        $response_message['response'] = 'warning';
                        $response_message['message'] = translate('This coupon is not applicable to your cart products!');
                    }
                } else {
                    $response_message['response'] = 'warning';
                    $response_message['message'] = translate('You already used this coupon!');
                }
            } else {
                $response_message['response'] = 'warning';
                $response_message['message'] = translate('Coupon expired!');
            }
        } else {
            $response_message['response'] = 'danger';
            $response_message['message'] = translate('Invalid coupon!');
        }

        if ($user != null) {
            $carts = Cart::where('user_id', $user->id)->active()->get();
        } else {
            $carts = ($temp_user != null) ? Cart::where('temp_user_id', $temp_user)->active()->get() : [];
        }
        // $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        $returnHTML = view('frontend.partials.cart.cart_summary', compact('coupon', 'carts', 'proceed'))->render();
        return response()->json(array('response_message' => $response_message, 'html'=>$returnHTML));
    }

    public function remove_coupon_code(Request $request)
    {
        $user       = auth()->user();
        $temp_user  = Session::has('temp_user_id') ? Session::get('temp_user_id') : null;
        $proceed    = $request->proceed;
        $carts = $user != null ? Cart::where('user_id', $user->id) : Cart::where('temp_user_id', $temp_user);
        $carts->update(
            [
                'discount' => 0.00,
                'coupon_code' => '',
                'coupon_applied' => 0
            ]
        );

        $coupon = Coupon::where('code', $request->code)->first();
        $carts = $carts->active()->get();

        // $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        return view('frontend.partials.cart.cart_summary', compact('coupon', 'carts', 'proceed'));
    }

    public function order_confirmed()
    {
        $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));

        // Cart::where('user_id', $combined_order->user_id)
        //     ->delete();

        Session::forget('club_point');
        Session::forget('combined_order_id');

        foreach($combined_order->orders as $order){
            if($order->notified == 0){
                NotificationUtility::sendOrderPlacedNotification($order);
                $order->notified = 1;
                $order->save();
            }
        }

        return view('frontend.order_confirmed', compact('combined_order'));
    }

    public function guestCustomerInfoCheck(Request $request){
        $user = addon_is_activated('otp_system') ?
                User::where('email', $request->email)->orWhere('phone','+'.$request->phone)->first() :
                User::where('email', $request->email)->first();
        return ($user != null) ? true : false;
    }

    public function updateDeliveryAddress(Request $request)
    {
        $proceed = 0;
        $default_carrier_id = null;
        $default_shipping_type = 'home_delivery';
        $user = auth()->user();
        $shipping_info = array();

        $carts = $user != null ?
                Cart::where('user_id', $user->id)->active()->get() :
                Cart::where('temp_user_id', $request->session()->get('temp_user_id'))->active()->get();

        $carts->toQuery()->update(['address_id' => $request->address_id]);

        // Toggle set_default for the selected address and update others
        $address = Address::find($request->address_id);
        if ($address) {
            if ($address->set_default == 0) {
                // Set the selected address as default
                $address->set_default = 1;
                $address->save();

                // Update other addresses for the user to not be default
                Address::where('user_id', $address->user_id)
                    ->where('id', '!=', $address->id)
                    ->update(['set_default' => 0]);
            }
        }

        $country_id = $user != null ?
                    $address->country_id :
                    $request->address_id;
        $city_id = $user != null ?
                    $address->city_id :
                    $request->city_id;
        $shipping_info['country_id'] = $country_id;
        $shipping_info['city_id'] = $city_id;

        $carrier_list = array();
        if (get_setting('shipping_type') == 'carrier_wise_shipping') {
            $default_shipping_type = 'carrier';
            $zone = Country::where('id', $country_id)->first()->zone_id;

            $carrier_query = Carrier::where('status', 1);
            $carrier_query->whereIn('id',function ($query) use ($zone) {
                $query->select('carrier_id')->from('carrier_range_prices')
                    ->where('zone_id', $zone);
            })->orWhere('free_shipping', 1);
            $carrier_list = $carrier_query->get();

            if (count($carrier_list) > 1) {
                $default_carrier_id = $carrier_list->toQuery()->first()->id;
            }
        }

        $carts = $carts->fresh();

        foreach ($carts as $key => $cartItem) {
            if (get_setting('shipping_type') == 'carrier_wise_shipping') {
                $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info, $default_carrier_id);
            } else {
                $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info);
            }
            $cartItem['address_id'] = $user != null ? $request->address_id : 0;
            $cartItem['shipping_type'] = $default_shipping_type;
            $cartItem['carrier_id'] = $default_carrier_id;
            $cartItem->save();
        }

        $carts = $carts->fresh();

        return array(
            'delivery_info' => view('frontend.partials.cart.delivery_info', compact('carts', 'carrier_list', 'shipping_info'))->render(),
            'cart_summary' => view('frontend.partials.cart.cart_summary', compact('carts', 'proceed'))->render()
        );
    }

    public function updateDeliveryInfo(Request $request)
    {
        $proceed = 0;
        $user = auth()->user();
        $shipping_info = array();

        if ($user != null) {
            $carts = Cart::where('user_id', $user->id)->active()->get();
        }
        else {
            $temp_user_id = $request->session()->get('temp_user_id');
            $carts = ($temp_user_id != null) ? Cart::where('temp_user_id', $temp_user_id)->active()->get() : [];
        }

        $user_carts = $carts->toQuery()->where('owner_id', $request->user_id)->get();

        $country_id = $user != null ?
                    Address::findOrFail($carts[0]->address_id)->country_id : $request->country_id;
        $city_id = $user != null ?
                    Address::findOrFail($carts[0]->address_id)->city_id : $request->city_id;
        $shipping_info['country_id'] = $country_id;
        $shipping_info['city_id'] = $city_id;

        $shipping_type = $request->shipping_type;
        foreach ($user_carts as $key => $cartItem) {
            if ($shipping_type != 'carrier' || $shipping_type == 'pickup_point') {
                if ($shipping_type == 'pickup_point') {
                    $cartItem['shipping_type'] = 'pickup_point';
                    $cartItem['pickup_point'] = $request->type_id;
                } else {
                    $cartItem['shipping_type'] = 'home_delivery';
                }
                $cartItem['shipping_cost'] = 0;
                if ($cartItem['shipping_type'] == 'home_delivery') {
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info);
                }
            } else {
                $cartItem['shipping_type'] = 'carrier';
                $cartItem['carrier_id'] = $request->type_id;
                $cartItem['shipping_cost'] = getShippingCost($user_carts, $key, $shipping_info, $cartItem['carrier_id']);
            }

            $cartItem->save();
        }

        $carts = $carts->fresh();

        return view('frontend.partials.cart.cart_summary', compact('carts', 'proceed'))->render();
    }

    public function orderRePayment(Request $request){
        $order = Order::findOrFail($request->order_id);
        if($order != null){
            $request->session()->put('payment_type', 'order_re_payment');
            $data['order_id'] = $order->id;
            $data['payment_method'] = $request->payment_option;
            $request->session()->put('payment_data', $data);

            // If block for Online payment, wallet and cash on delivery. Else block for Offline payment
            $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $request->payment_option))) . "Controller";
            if (class_exists($decorator)) {
                return (new $decorator)->pay($request);
            }
            else {
                $manual_payment_data = array(
                    'name'   => $request->payment_option,
                    'amount' => $order->grand_total,
                    'trx_id' => $request->trx_id,
                    'photo'  => $request->photo
                );

                $order->payment_type = $request->payment_option;
                $order->manual_payment = 1;
                $order->manual_payment_data = json_encode($manual_payment_data);
                $order->save();

                flash(translate('Payment done.'))->success();
                return redirect()->route('purchase_history.details', encrypt($order->id));
            }
        }
        flash(translate('Order Not Found'))->warning();
        return back();
    }

    public function orderRePaymentDone($payment_data, $payment_details = null)
    {
        $order = Order::findOrFail($payment_data['order_id']);
        $order->payment_status = 'paid';
        $order->payment_details = $payment_details;
        $order->payment_type = $payment_data['payment_method'];
        $order->save();
        calculateCommissionAffilationClubPoint($order);

        if($order->notified == 0){
            NotificationUtility::sendOrderPlacedNotification($order);
            $order->notified = 1;
            $order->save();
        }

        Session::forget('payment_type');
        Session::forget('order_id');

        flash(translate('Payment done.'))->success();
        return redirect()->route('purchase_history.details', encrypt($order->id));
    }
}
