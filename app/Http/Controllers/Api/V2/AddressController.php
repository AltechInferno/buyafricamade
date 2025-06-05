<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\City;
use App\Models\Country;
use App\Http\Resources\V2\AddressCollection;
use App\Models\Address;
use App\Http\Resources\V2\CitiesCollection;
use App\Http\Resources\V2\StatesCollection;
use App\Http\Resources\V2\CountriesCollection;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\State;
use App\Models\User;
class AddressController extends Controller
{
    public function addresses()
    {
        return new AddressCollection(Address::where('user_id', auth()->user()->id)->get());
    }

    public function createShippingAddress(Request $request)
    {
        $address = new Address;
        $address->user_id = auth()->user()->id;
        $address->address = $request->address;
        $address->country_id = $request->country_id;
        $address->state_id = $request->state_id;
        $address->city_id = $request->city_id;
        $address->postal_code = $request->postal_code;
        $address->phone = $request->phone;
        $address->save();

        return response()->json([
            'result' => true,
            'message' => translate('Shipping information has been added successfully')
        ]);
    }

    public function updateShippingAddress(Request $request)
    {
        $address = Address::find($request->id);
        $address->address = $request->address;
        $address->country_id = $request->country_id;
        $address->state_id = $request->state_id;
        $address->city_id = $request->city_id;
        $address->postal_code = $request->postal_code;
        $address->phone = $request->phone;
        $address->save();

        return response()->json([
            'result' => true,
            'message' => translate('Shipping information has been updated successfully')
        ]);
    }

    public function updateShippingAddressLocation(Request $request)
    {
        $address = Address::find($request->id);
        $address->latitude = $request->latitude;
        $address->longitude = $request->longitude;
        $address->save();

        return response()->json([
            'result' => true,
            'message' => translate('Shipping location in map updated successfully')
        ]);
    }


    public function deleteShippingAddress($id)
    {
        $address = Address::where('id',$id)->where('user_id',auth()->user()->id)->first();
        if($address == null) {
            return response()->json([
                'result' => false,
                'message' => translate('Address not found')
            ]);
        }
        $address->delete();
        return response()->json([
            'result' => true,
            'message' => translate('Shipping information has been deleted')
        ]);
    }

    public function makeShippingAddressDefault(Request $request)
    {
        Address::where('user_id', auth()->user()->id)->update(['set_default' => 0]); //make all user addressed non default first

        $address = Address::find($request->id);
        $address->set_default = 1;
        $address->save();
        return response()->json([
            'result' => true,
            'message' => translate('Default shipping information has been updated')
        ]);
    }

    public function updateAddressInCart(Request $request)
    {
        $authUser = $request->user_id != null ? User::where('id', $request->user_id)->first() : null;
        $address[] = null;
        if(get_setting('guest_checkout_activation') == 0 && $authUser == null){
            return response()->json([
                'result' => false,
                'message' => translate('Please Login First.')
            ]);
        }

        if($authUser != null){
            if($request->address_id == null){
                return response()->json([
                    'result' => false,
                    'message' => translate('Please add shipping address.')
                ]);
            }
            Cart::where('user_id', $authUser->id)->active()->update(['address_id' => $request->address_id]);
            $shipping_info['address_id'] = $request->address_id;
            return response()->json([
                'result' => true,
                'data' => $shipping_info,
                'message' => translate('Address is saved')
            ]);
        }
        else
        {
            if(get_setting('guest_checkout_activation') == 1){
                if($request->name == null || $request->email == null || $request->address == null ||
                    $request->country_id == null || $request->state_id == null || $request->city_id == null ||
                        $request->postal_code == null || $request->phone == null)
                {
                    return response()->json([
                        'result' => false,
                        'message' => translate('Please add shipping address')
                    ]);
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

                return response()->json([
                    'result' => true,
                    'data' => $shipping_info,
                    'message' => translate('Shipping Info saved.')
                ]);
            }
        }
    }

    public function getShippingInCart(Request $request)
    {
        $cart= Cart::where('user_id', auth()->user()->id)->active()->first();
        $address = $cart->address;
        return new AddressCollection(Address::where('id', $address->id)->get());
    }

    public function updateShippingTypeInCart(Request $request)
    {
        try {
            $userId        = $request->has('user_id') ? $request->user_id : null;
            $tempUserId    = $request->has('temp_user_id') ? $request->temp_user_id : null;
            $carts         = ($userId != null) ? Cart::where('user_id', $userId)->active()->get() : Cart::where('temp_user_id', $tempUserId)->active()->get();

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

            foreach ($carts as $key => $cart) {
                $cart->shipping_cost = 0;
                if($request->shipping_type=="pickup_point"){
                    $cart->shipping_type="pickup_point";
                    $cart->pickup_point=$request->shipping_id;
                    $cart->carrier_id=0;
                }
                else if($request->shipping_type=="home_delivery"){
                    $cart->shipping_cost = getShippingCost($carts, $key, $shipping_info );
                    $cart->shipping_type="home_delivery";
                    $cart->pickup_point=0;
                    $cart->carrier_id=0;
                }
                else if($request->shipping_type=="carrier_base"){
                    $cart->shipping_cost = getShippingCost($carts, $key, $shipping_info, $cart->carrier_id);
                    $cart->shipping_type="carrier";
                    $cart->carrier_id=$request->shipping_id;
                    $cart->pickup_point=0;
                }
                $cart->save();
            }
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => translate('Could not save the address')
            ]);
        }
        return response()->json([
            'result' => true,
            'message' => translate('Delivery address is saved')
        ]);
    }

    public function getCities()
    {
        return new CitiesCollection(City::where('status', 1)->get());
    }

    public function getStates()
    {
        return new StatesCollection(State::where('status', 1)->get());
    }

    public function getCountries(Request $request)
    {
        $country_query = Country::where('status', 1);
        if ($request->name != "" || $request->name != null) {
            $country_query->where('name', 'like', '%' . $request->name . '%');
        }
        $countries = $country_query->get();

        return new CountriesCollection($countries);
    }

    public function getCitiesByState($state_id,Request $request)
    {
        $city_query = City::where('status', 1)->where('state_id',$state_id);
        if ($request->name != "" || $request->name != null) {
            $city_query->where('name', 'like', '%' . $request->name . '%');
        }
        $cities = $city_query->get();
        return new CitiesCollection($cities);
    }

    public function getStatesByCountry($country_id,Request $request)
    {
        $state_query = State::where('status', 1)->where('country_id',$country_id);
        if ($request->name != "" || $request->name != null) {
            $state_query->where('name', 'like', '%' . $request->name . '%');
        }
        $states = $state_query->get();
        return new StatesCollection($states);
    }
}
