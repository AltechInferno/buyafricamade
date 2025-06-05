@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
      <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Manage Profile') }}</h1>
        </div>
      </div>
    </div>
    <form action="{{ route('seller.profile.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        <input name="_method" type="hidden" value="POST">
        @csrf
        <!-- Basic Info-->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Basic Info')}}</h5>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="name">{{ translate('Your Name') }}</label>
                    <div class="col-md-10">
                        <input type="text" name="name" value="{{ $user->name }}" id="name" class="form-control" placeholder="{{ translate('Your Name') }}" required>
                        @error('name')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="phone">{{ translate('Your Phone') }}</label>
                    <div class="col-md-10">
                        <input type="text" name="phone" value="{{ $user->phone }}" id="phone" class="form-control" placeholder="{{ translate('Your Phone')}}">
                        @error('phone')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label">{{ translate('Photo') }}</label>
                    <div class="col-md-10">
                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                            <div class="input-group-prepend">
                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                            </div>
                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                            <input type="hidden" name="photo" value="{{ $user->avatar_original }}" class="selected-files">
                        </div>
                        <div class="file-preview box sm">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="password">{{ translate('Your Password') }}</label>
                    <div class="col-md-10">
                        <input type="password" name="new_password" id="password" class="form-control" placeholder="{{ translate('New Password') }}">
                        @error('new_password')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-md-2 col-form-label" for="confirm_password">{{ translate('Confirm Password') }}</label>
                    <div class="col-md-10">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="{{ translate('Confirm Password') }}" >
                        @error('confirm_password')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

        <!-- Payment System -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Payment Setting')}}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <label class="col-md-3 col-form-label">{{ translate('Cash Payment') }}</label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-3">
                            <input value="1" name="cash_on_delivery_status" type="checkbox" @if ($user->shop->cash_on_delivery_status == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label">{{ translate('Bank Payment') }}</label>
                    <div class="col-md-9">
                        <label class="aiz-switch aiz-switch-success mb-3">
                            <input value="1" name="bank_payment_status" type="checkbox" @if ($user->shop->bank_payment_status == 1) checked @endif>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_name">{{ translate('Bank Name') }}</label>
                    <div class="col-md-9">
                        <input type="text" name="bank_name" value="{{ $user->shop->bank_name }}" id="bank_name" class="form-control mb-3" placeholder="{{ translate('Bank Name')}}">
                        @error('phone')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_acc_name">{{ translate('Bank Account Name') }}</label>
                    <div class="col-md-9">
                        <input type="text" name="bank_acc_name" value="{{ $user->shop->bank_acc_name }}" id="bank_acc_name" class="form-control mb-3" placeholder="{{ translate('Bank Account Name')}}">
                        @error('bank_acc_name')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_acc_no">{{ translate('Bank Account Number') }}</label>
                    <div class="col-md-9">
                        <input type="text" name="bank_acc_no" value="{{ $user->shop->bank_acc_no }}" id="bank_acc_no" class="form-control mb-3" placeholder="{{ translate('Bank Account Number')}}">
                        @error('bank_acc_no')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <label class="col-md-3 col-form-label" for="bank_routing_no">{{ translate('Bank Routing Number') }}</label>
                    <div class="col-md-9">
                        <input type="number" name="bank_routing_no" value="{{ $user->shop->bank_routing_no }}" id="bank_routing_no" lang="en" class="form-control mb-3" placeholder="{{ translate('Bank Routing Number')}}">
                        @error('bank_routing_no')
                            <small class="form-text text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group mb-0 text-right">
            <button type="submit" class="btn btn-primary">{{translate('Update Profile')}}</button>
        </div>
    </form>

    <br>

    <!-- Address -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Address')}}</h5>
        </div>
        <div class="card-body">
            <div class="row gutters-10">
                @foreach ($addresses as $key => $address)
                    <div class="col-lg-4">
                        <div class="border p-3 pr-5 rounded mb-3 position-relative">
                            <div>
                                <span class="w-50 fw-600">{{ translate('Address') }}:</span>
                                <span class="ml-2">{{ $address->address }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('Postal Code') }}:</span>
                                <span class="ml-2">{{ $address->postal_code }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('City') }}:</span>
                                <span class="ml-2">{{ $address->cityName}}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('State') }}:</span>
                                <span class="ml-2">{{ $address->stateName }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('Country') }}:</span>
                                <span class="ml-2">{{ optional($address->country)->name }}</span>
                            </div>
                            <div>
                                <span class="w-50 fw-600">{{ translate('Phone') }}:</span>
                                <span class="ml-2">{{ $address->phone }}</span>
                            </div>
                            @if ($address->set_default)
                                <div class="position-absolute right-0 bottom-0 pr-2 pb-3">
                                    <span class="badge badge-inline badge-primary">{{ translate('Default') }}</span>
                                </div>
                            @endif
                            <div class="dropdown position-absolute right-0 top-0">
                                <button class="btn bg-gray px-2" type="button" data-toggle="dropdown">
                                    <i class="la la-ellipsis-v"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" onclick="edit_address('{{$address->id}}')">
                                        {{ translate('Edit') }}
                                    </a>
                                    @if (!$address->set_default)
                                        <a class="dropdown-item" href="{{ route('seller.addresses.set_default', $address->id) }}">{{ translate('Make This Default') }}</a>
                                    @endif
                                    <a class="dropdown-item" href="{{ route('seller.addresses.destroy', $address->id) }}">{{ translate('Delete') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="col-lg-4 mx-auto" onclick="add_new_address()">
                    <div class="border p-3 rounded mb-3 c-pointer text-center bg-light">
                        <i class="la la-plus la-2x"></i>
                        <div class="alpha-7">{{ translate('Add New Address') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Email -->
    <form action="{{ route('user.change.email') }}" method="POST">
        @csrf
        <div class="card">
          <div class="card-header">
              <h5 class="mb-0 h6">{{ translate('Change your email')}}</h5>
          </div>
          <div class="card-body">
              <div class="row">
                  <div class="col-md-2">
                      <label>{{ translate('Your Email') }}</label>
                  </div>
                  <div class="col-md-10">
                      <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="{{ translate('Your Email')}}" name="email" value="{{ $user->email }}" />
                        <div class="input-group-append">
                           <button type="button" class="btn btn-outline-secondary new-email-verification">
                               <span class="d-none loading">
                                   <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>{{ translate('Sending Email...') }}
                               </span>
                               <span class="default">{{ translate('Verify') }}</span>
                           </button>
                        </div>
                      </div>
                      <div class="form-group mb-0 text-right">
                          <button type="submit" class="btn btn-primary">{{translate('Update Email')}}</button>
                      </div>
                  </div>
              </div>
          </div>
        </div>
    </form>

@endsection

@section('modal')
    {{-- New Address Modal --}}
    <div class="modal fade" id="new-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form class="form-default" role="form" action="{{ route('seller.addresses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="p-3">
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Address')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <textarea class="form-control mb-3" placeholder="{{ translate('Your Address')}}" rows="2" name="address" required></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Country')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" required>
                                            <option value="">{{ translate('Select your country') }}</option>
                                            @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                                <option value="{{ $country->name }}" data-country-id="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <input type="number" id="countryId" name="countryId" value="" hidden/>

                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('State')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="state" id="state" required>
                                        <option value="">{{ translate('Select State') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('City')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city" id="city" required>
                                        <option value="">{{ translate('Select City') }}</option>
                                    </select>
                                </div>
                            </div>

                            <input id="cityCode" name="cityCode" type="text" value="" hidden/>

                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Delivery Town')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="townName" id="townName" required>
                                        <option value="">{{ translate('Select Delivery Town') }}</option>
                                    </select>
                                </div>
                            </div>

                            <input id="townId" name="townId" type="text" value="" hidden/>

                            @if (get_setting('google_map') == 1)
                                <div class="row">
                                    <input id="searchInput" class="controls" type="text" placeholder="{{translate('Enter a location')}}">
                                    <div id="map"></div>
                                    <ul id="geoData">
                                        <li style="display: none;">{{ translate('Full Address') }}: <span id="location"></span></li>
                                        <li style="display: none;">{{ translate('Postal Code') }}: <span id="postal_code"></span></li>
                                        <li style="display: none;">{{ translate('Country') }}: <span id="country"></span></li>
                                        <li style="display: none;">{{ translate('Latitude') }}: <span id="lat"></span></li>
                                        <li style="display: none;">{{ translate('Longitude') }}: <span id="lon"></span></li>
                                    </ul>
                                </div>

                                <div class="row">
                                    <div class="col-md-2" id="">
                                        <label for="exampleInputuname">{{ translate('Longitude') }}</label>
                                    </div>
                                    <div class="col-md-10" id="">
                                        <input type="text" class="form-control mb-3" id="longitude" name="longitude" readonly="">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-2" id="">
                                        <label for="exampleInputuname">{{ translate('Latitude') }}</label>
                                    </div>
                                    <div class="col-md-10" id="">
                                        <input type="text" class="form-control mb-3" id="latitude" name="latitude" readonly="">
                                    </div>
                                </div>
                            @endif

                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Postal code')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('Your Postal Code')}}" name="postal_code" value="" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label>{{ translate('Phone')}}</label>
                                </div>
                                <div class="col-md-10">
                                    <input type="text" class="form-control mb-3" placeholder="{{ translate('+880')}}" name="phone" value="" required>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Address Modal --}}
    <div class="modal fade" id="edit-address-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('New Address') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="edit_modal_body">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">

        $('.new-email-verification').on('click', function() {
            $(this).find('.loading').removeClass('d-none');
            $(this).find('.default').addClass('d-none');
            var email = $("input[name=email]").val();

            $.post('{{ route('user.new.verify') }}', {_token:'{{ csrf_token() }}', email: email}, function(data){
                data = JSON.parse(data);
                $('.default').removeClass('d-none');
                $('.loading').addClass('d-none');
                if(data.status == 2)
                    AIZ.plugins.notify('warning', data.message);
                else if(data.status == 1)
                    AIZ.plugins.notify('success', data.message);
                else
                    AIZ.plugins.notify('danger', data.message);
            });
        });

        function add_new_address(){
            $('#new-address-modal').modal('show');
        }

        function edit_address(address) {
            var url = '{{ route("seller.addresses.edit", ":id") }}';
            url = url.replace(':id', address);

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: 'GET',
                success: function (response) {
                    $('#edit_modal_body').html(response.html);
                    $('#edit-address-modal').modal('show');
                    AIZ.plugins.bootstrapSelect('refresh');

                    @if (get_setting('google_map') == 1)
                        var lat     = -33.8688;
                        var long    = 151.2195;

                        if(response.data.address_data.latitude && response.data.address_data.longitude) {
                            lat     = parseFloat(response.data.address_data.latitude);
                            long    = parseFloat(response.data.address_data.longitude);
                        }

                        initialize(lat, long, 'edit_');
                    @endif
                }
            });
        }

        $(document).on('change', '[name=country_id]', function() {
        var country_id = $(this).val();
        var countryId = $(this).find(':selected').data('country-id'); // Get the country ID
        $('#countryId').val(countryId); // Set the countryId input value
        get_states(country_id);
    });

    $(document).on('change', '[name=state]', function() {
        var state = $(this).val();
        console.log('selected state', state)
        get_city(state);
    });

    function get_states(country_id) {
        $('[name="state"]').html(""); // Clear the dropdown
        if (country_id == "Nigeria") { // Check if Nigeria is selected
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('seller.get-states', ['country_id' => ':country_id']) }}".replace(':country_id', country_id), // Correct URL construction
                type: 'GET',
                success: function (response) {
                    if (response.length > 0) {
                        let options = '<option value="">{{ translate("Select State") }}</option>';
                        response.forEach(state => {
                            options += `<option value="${state.StateName}">${state.StateName}</option>`;
                        });
                        $('[name="state"]').html(options); // Populate the dropdown
                        AIZ.plugins.bootstrapSelect('refresh'); // Refresh the select picker
                    }
                },
                error: function () {
                    AIZ.plugins.notify('danger', '{{ translate("Failed to fetch states.") }}');
                }
            });
        }
    }



    function get_city(state) {
        $('[name="city"]').html(""); // Clear the city dropdown

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{ route('seller.get-cities-by-state', ['state' => ':state']) }}".replace(':state', state),
            type: 'GET',
            success: function (response) {
                if (response.length > 0) {
                    let options = '<option value="">{{ translate("Select City") }}</option>';
                    response.forEach(city => {
                        options += `<option value="${city.CityName}" data-city-code="${city.CityCode}">${city.CityName}</option>`;
                    });
                    $('[name="city"]').html(options); // Populate the city dropdown
                    AIZ.plugins.bootstrapSelect('refresh'); // Refresh the select picker
                }
            },
            error: function () {
                AIZ.plugins.notify('danger', '{{ translate("Failed to fetch cities.") }}');
            }
        });
    }


    function get_delivery_town(cityCode) {
        $('[name="townName"]').html(""); // Clear the city dropdown

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('get-delivery-towns') }}/" + cityCode, // Construct the URL with the state name
            type: 'GET',
            success: function (response) {
                if (response.length > 0) {
                    let options = '<option value="">{{ translate("Select Delivery Town") }}</option>';
                    response.forEach(town => {
                        options += `<option value="${town.TownName}" data-town-id="${town.TownID}">${town.TownName}</option>`;
                    });
                    $('[name="townName"]').html(options); // Populate the city dropdown
                    AIZ.plugins.bootstrapSelect('refresh'); // Refresh the select picker
                }
            },
            error: function () {
                AIZ.plugins.notify('danger', '{{ translate("Failed to fetch delivery towns.") }}');
            }
        });
    }

    $(document).on('change', '[name="city"]', function() {
        var cityCode = $(this).find(':selected').data('city-code');
        $('#cityCode').val(cityCode); // Set the cityCode input value
        get_delivery_town(cityCode)
    });


    $(document).on('change', '[name="townName"]', function() {
        var townId = $(this).find(':selected').data('town-id');
        $('#townId').val(townId); // Set the townId input value
    });
    </script>

    @if (get_setting('google_map') == 1)

        @include('frontend.partials.google_map')

    @endif

@endsection
