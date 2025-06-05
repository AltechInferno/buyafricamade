<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\City;
use App\Models\State;
use App\Models\Cart;
use Auth;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{


    public function generateToken()
    {
        $url = 'http://api.clicknship.com.ng/Token';
        $data = [
            'username' => 'MATEAFRICA',
            'password' => 'Tywqbj$389gub',
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $address = new Address;
        if ($request->has('customer_id')) {
            $address->user_id   = $request->customer_id;
        } else {
            $address->user_id   = Auth::user()->id;
        }
        $address->address       = $request->address;
        $address->country_id    = $request->countryId;
        $address->stateName     = $request->state;
        $address->cityName      = $request->city;
        $address->cityCode      = $request->cityCode;
        $address->townName      = $request->townName;
        $address->townId        = $request->townId;
        $address->state_id      = $request->state_id;
        $address->city_id       = $request->city_id;
        $address->longitude     = $request->longitude;
        $address->latitude      = $request->latitude;
        $address->postal_code   = $request->postal_code;
        $address->phone         = '+'.$request->country_code.$request->phone;
        $address->save();

        flash(translate('Address info Stored successfully'))->success();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // public function edit($id)
    // {
    //     $data['address_data'] = Address::findOrFail($id);
    //     $data['states'] = State::where('status', 1)->where('country_id', $data['address_data']->country_id)->get();
    //     $data['cities'] = City::where('status', 1)->where('state_id', $data['address_data']->state_id)->get();

    //     $returnHTML = view('frontend.partials.address.address_edit_modal', $data)->render();
    //     return response()->json(array('data' => $data, 'html' => $returnHTML));
    //     //        return ;
    // }

    public function edit($id)
{
    $data['address_data'] = Address::findOrFail($id);

    // Fetch states using the getCSStates function
    $statesResponse = $this->getCSStates($data['address_data']->country_id);
    $data['states'] = json_decode($statesResponse->getContent(), true);

    // Fetch cities using the getCSCitiesByState function
    $citiesResponse = $this->getCSCitiesByState($data['address_data']->stateName);
    $data['cities'] = json_decode($citiesResponse->getContent(), true);

    // Fetch delivery towns using the getDeliveryTown function
    $deliveryTownsResponse = $this->getDeliveryTown($data['address_data']->cityCode);
    $data['delivery_towns'] = json_decode($deliveryTownsResponse->getContent(), true);

    $returnHTML = view('frontend.partials.address.address_edit_modal', $data)->render();
    return response()->json(array('data' => $data, 'html' => $returnHTML));
}

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $address = Address::findOrFail($id);

        $address->address       = $request->address;
        $address->country_id    = $request->country_id;
        $address->state_id      = $request->state_id;
        $address->city_id       = $request->city_id;
        $address->longitude     = $request->longitude;
        $address->latitude      = $request->latitude;
        $address->postal_code   = $request->postal_code;
        $address->phone         = $request->phone;

        $address->save();

        flash(translate('Address info updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $address = Address::findOrFail($id);
        if (!$address->set_default) {
            $address->delete();
            return back();
        }
        flash(translate('Default address cannot be deleted'))->warning();
        return back();
    }


    public function getCSStates($country_id)
    {
        // Generate the token
        $tokenData = $this->generateToken();
        $token = $tokenData['access_token'];

        // Fetch states from ClicknShip API
        $url = 'http://api.clicknship.com.ng/clicknship/Operations/States';
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ]);

        $states = json_decode($response->getBody(), true);

        return response()->json($states);
    }


    public function getCSCitiesByState($state)
    {
        // Generate the token
        $tokenData = $this->generateToken();
        $token = $tokenData['access_token'];

        // Fetch states from ClicknShip API
        $url = "http://api.clicknship.com.ng/clicknship/Operations/StateCities?StateName={$state}";
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ]);

        $cities = json_decode($response->getBody(), true);

        return response()->json($cities);
    }


    public function getDeliveryTown($cityCode)
    {
        // Generate the token
        $tokenData = $this->generateToken();
        $token = $tokenData['access_token'];

        // Fetch states from ClicknShip API
        $url = "http://api.clicknship.com.ng/clicknship/Operations/DeliveryTowns?CityCode={$cityCode}";
        $client = new \GuzzleHttp\Client();
        $response = $client->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ],
        ]);

        $delivery_towns = json_decode($response->getBody(), true);

        return response()->json($delivery_towns);
    }


        /**
     * Calculate delivery fee based on origin and destination.
     *
     * @param  string  $originTown
     * @param  string  $destinationTown
     * @return float
     */
     
     
    public function calculateDeliveryFee($originCity, $destinationCity, $cartId)
    {

        $tokenData = $this->generateToken();
        $token = $tokenData['access_token'];

        // Prepare the request body
        $info = [
            'Origin' => $originCity,
            'Destination' => $destinationCity,
            'Weight' => '1.5',
            'PickupType' => '1'
        ];

        // Check if all required fields are present
        if (empty($info['Origin']) || empty($info['Destination']) || empty($info['Weight']) || empty($info['PickupType'])) {
            return response()->json(['msg' => 'Conditions not met!', 'status' => 400], 400);
        }

        // Fetch delivery fee from ClicknShip API
        $url = 'http://api.clicknship.com.ng/clicknship/Operations/DeliveryFee';
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $info,
            ]);

            $responseData = json_decode($response->getBody(), true);

            // Extract the TotalAmount from the response
            $totalAmount = $responseData ?? null;

            if ($totalAmount !== null) {
                //update the cart with the delivery fee
                $totalFee = $responseData[0]['TotalAmount'] ?? null;
                $cart = Cart::findOrFail($cartId);
                $cart->shipping_cost = $totalFee;
                $cart->save();

                // Log::info('Total Amount:', ['totalAmount' => $totalAmount]);

                return response()->json(['delivery_fee' => $totalAmount, 'status' => 200, 'msg' => 'success'], 200);
            } else {
                return response()->json(['msg' => 'Failed to retrieve total amount', 'status' => 500], 500);
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            \Log::error('500 internal error', ['error' => $e->getMessage()]);
            return response()->json(['msg' => '500 internal error', 'status' => 500], 500);
        }
    }
    
    
    // Function to handle delivery type selection
    public function handleDeliveryTypeSelection(Request $request)
    {
        $buyerAddresses = Auth::user()->addresses;

        if ($buyerAddresses->count() > 1) {
            $buyerAddress = $buyerAddresses->where('set_default', 1)->first();
        } else {
            $buyerAddress = $buyerAddresses->first();
        }

        $ownerAddress = Address::where('user_id', $request->owner_id)->first();
        // $buyerAddress = Auth::user()->addresses->first();

        if ($ownerAddress && $buyerAddress) {
            $originCity = $ownerAddress->cityName;
            $destinationCity = $buyerAddress->cityName;
            $cartId = $request->cart_id;

            Log::info('Destination Town:', ['destination' => $destinationCity]);
            Log::info('Origin Town:', ['origin' => $originCity]);

            $deliveryFee = $this->calculateDeliveryFee($originCity, $destinationCity, $cartId);

            // Return or process the delivery fee as needed
            return response()->json(['delivery_fee' => $deliveryFee]);
        }

        return response()->json(['error' => 'Addresses not found'], 404);
    }


    public function getStates(Request $request)
    {
        $states = State::where('status', 1)->where('country_id', $request->country_id)->get();
        $html = '<option value="">' . translate("Select State") . '</option>';

        foreach ($states as $state) {
            $html .= '<option value="' . $state->id . '">' . $state->name . '</option>';
        }

        echo json_encode($html);
    }

    public function getCities(Request $request)
    {
        $cities = City::where('status', 1)->where('state_id', $request->state_id)->get();
        $html = '<option value="">' . translate("Select City") . '</option>';

        foreach ($cities as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->getTranslation('name') . '</option>';
        }

        echo json_encode($html);
    }
    
    

    public function set_default($id)
    {
        foreach (Auth::user()->addresses as $key => $address) {
            $address->set_default = 0;
            $address->save();
        }
        $address = Address::findOrFail($id);
        $address->set_default = 1;
        $address->save();

        return back();
    }
}
