<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Address;
use App\Models\City;
use App\Models\State;
use Auth;

class AddressController extends Controller
{

    public function generateToken()
    {
        $url = 'http://api.clicknship.com.ng/Token';
        $data = [
            'username' => 'cnsdemoapiacct',
            'password' => 'ClickNShip$12345',
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $address = new Address;
        $address->user_id       = Auth::user()->id;
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

        return back();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['address_data'] = Address::findOrFail($id);
        $data['states'] = State::where('status', 1)->where('country_id', $data['address_data']->country_id)->get();
        $data['cities'] = City::where('status', 1)->where('state_id', $data['address_data']->state_id)->get();

        $returnHTML = view('seller.profile.address_edit_modal', $data)->render();
        return response()->json(array('data' => $data, 'html'=>$returnHTML));
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
        if(!$address->set_default){
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


    public function getStates(Request $request) {
        $states = State::where('status', 1)->where('country_id', $request->country_id)->get();
        $html = '<option value="">'.translate("Select State").'</option>';

        foreach ($states as $state) {
            $html .= '<option value="' . $state->id . '">' . $state->name . '</option>';
        }

        echo json_encode($html);
    }

    public function getCities(Request $request) {
        $cities = City::where('status', 1)->where('state_id', $request->state_id)->get();
        $html = '<option value="">'.translate("Select City").'</option>';

        foreach ($cities as $row) {
            $html .= '<option value="' . $row->id . '">' . $row->getTranslation('name') . '</option>';
        }

        echo json_encode($html);
    }

    public function set_default($id){
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
