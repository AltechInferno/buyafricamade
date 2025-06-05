<script type="text/javascript">

    function submitShippingInfoForm(el) {
        var email = $("input[name='email']").val();
        var phone = $("input[name='country_code']").val()+$("input[name='phone']").val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('guest_customer_info_check')}}",
            type: 'POST',
            data: {
                email : email,
                phone : phone
            },
            success: function (response) {
                if(response ==  1){
                    $('#login_modal').modal();
                    AIZ.plugins.notify('warning', '{{ translate('You already have an account with this information. Please Login first.') }}');
                }
                else{
                    $('#shipping_info_form').submit();
                }
            }
        });
    }

    function add_new_address(){
        $('#new-address-modal').modal('show');
    }

    function edit_address(address) {
        var url = '{{ route("addresses.edit", ":id") }}';
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
                url: "{{ route('get-states', ['country_id' => ':country_id']) }}".replace(':country_id', country_id), // Correct URL construction
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
            url: "{{ url('get-cities-by-state') }}/" + state, // Construct the URL with the state name
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
