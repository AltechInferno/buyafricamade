<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Cart;
use App\Models\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Country;

class CarrierController extends Controller
{
    public function index(Request $request)
    {
        $seller_wise_carrier_list = array();
        $userId        = $request->has('user_id') ? $request->user_id : null;
        $tempUserId    = $request->has('temp_user_id') ? $request->temp_user_id : null;
        $carts = ($userId != null) ? Cart::where('user_id', $userId)->active()->get() : Cart::where('temp_user_id', $tempUserId)->active()->get();

        // Logged In User shipping info
        if($userId != null){
            $address = Address::where('id', $carts[0]['address_id'])->first();
            $shipping_info['country_id'] = $address->country_id;
            $shipping_info['city_id'] = $address->city_id;
        }

        // Guest User Shipping info
        elseif($tempUserId != null){
            $shipping_info['country_id'] = $request->country_id;
            $shipping_info['city_id'] = $request->city_id;
        }

        if (count($carts) > 0) {
            $zone = $shipping_info['country_id'] ? Country::where('id', $shipping_info['country_id'])->first()->zone_id : null;
            $carrier_query = Carrier::query();
            $carrier_query->whereIn('id',function ($query) use ($zone) {
                $query->select('carrier_id')->from('carrier_range_prices')
                ->where('zone_id', $zone);
            })->orWhere('free_shipping', 1);
            $carriers_list = $carrier_query->active()->get();
            foreach($carts->unique('owner_id') as $cart) {
                $new_carrier_list = [];
                foreach($carriers_list as  $carrier_list) {
                    $new_carrier_list['id']            = $carrier_list->id;
                    $new_carrier_list['name']          = $carrier_list->name;
                    $new_carrier_list['logo']          = uploaded_asset($carrier_list->logo);
                    $new_carrier_list['transit_time']  = (integer) $carrier_list->transit_time;
                    $new_carrier_list['free_shipping'] = $carrier_list->free_shipping == 1 ? true : false;
                    $new_carrier_list['transit_price'] = carrier_base_price($carts, $carrier_list->id, $cart->owner_id, $shipping_info);

                    $seller_wise_carrier_list[$cart->owner_id][] = $new_carrier_list;
                }
            }
        }
        return response()->json([
            'data'    => $seller_wise_carrier_list,
            'success' => true,
            'status'  => 200
        ]);
    }
}
