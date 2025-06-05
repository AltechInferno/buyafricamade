<div class="row gutters-16">
    @php
        $physical = false;
        $col_val = 'col-12';
        foreach ($products as $key => $cartItem){
            $product = get_single_product($cartItem);
            if ($product->digital == 0) {
                $physical = true;
                $col_val = 'col-md-6';
            }
        }
    @endphp

    @php
        $owner_id = $carts->first()->owner_id;
        $address = \App\Models\Address::where('user_id', $owner_id)->first();

    @endphp


    <!-- Product List -->
    <div class="{{ $col_val }}">
        <ul class="list-group list-group-flush mb-3">
            @foreach ($products as $key => $cartItem)
                @php
                    $product = get_single_product($cartItem);
                @endphp
                <li class="list-group-item pl-0 py-3 border-0">
                    <div class="d-flex align-items-center">
                        <span class="mr-2 mr-md-3">
                            <img src="{{ get_image($product->thumbnail) }}"
                                class="img-fit size-60px"
                                alt="{{  $product->getTranslation('name')  }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                        </span>
                        <span class="fs-14 fw-400 text-dark">
                            <span class="text-truncate-2">{{ $product->getTranslation('name') }}</span>
                            @if ($product_variation[$key] != '')
                                <span class="fs-12 text-secondary">{{ translate('Variation') }}: {{ $product_variation[$key] }}</span>
                            @endif
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    @if ($physical)
        <!-- Choose Delivery Type -->
        <div class="col-md-6 mb-2">
            <h6 class="fs-14 fw-700 mt-3">{{ translate('Choose Delivery Type') }}</h6>

            <div class="row gutters-16">
                <!-- Home Delivery -->
                <div class="col-6">
                    <label class="aiz-megabox d-block bg-white mb-0">
                        <input
                            type="radio"
                            name="shipping_type_{{ $owner_id }}"
                            value="home_delivery"
                            onchange="updateDeliveryFee({{ $owner_id }}, {{$carts->first()->id}})"
                            data-target=".pickup_point_id_{{ $owner_id }}"
                            required
                        >
                        <span class="d-flex aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">
                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                            <span class="flex-grow-1 pl-3 fw-600">{{  translate('Home Delivery') }}</span>
                        </span>
                    </label>
                </div>
                <!-- Local Pickup -->
                <div class="col-6">
                    <label class="aiz-megabox d-block bg-white mb-0">
                        <input
                            type="radio"
                            name="shipping_type_{{ $owner_id }}"
                            value="pickup_point"
                            onchange="updateDeliveryFee({{ $owner_id }}, {{$carts->first()->id}})"
                            data-target=".pickup_point_id_{{ $owner_id }}"
                            required
                        >
                        <span class="d-flex aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">
                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                            <span class="flex-grow-1 pl-3 fw-600">{{  translate('Local Pickup') }}</span>
                        </span>
                    </label>
                </div>
        </div>

        <!-- Display the delivery fee -->
        <div id="delivery-fee-display" class="mt-3">
            <strong>{{ translate('Delivery Fee:') }}</strong> <span id="delivery-fee-amount">0.00</span>
        </div>


        </div>
    @endif
</div>


<script>

document.addEventListener('DOMContentLoaded', function() {
    // Automatically select "Home Delivery" radio button
    var homeDeliveryRadio = document.querySelector('input[name="shipping_type_{{ $owner_id }}"][value="home_delivery"]');
    if (homeDeliveryRadio) {
        homeDeliveryRadio.checked = true;
        // Trigger the delivery fee calculation
        updateDeliveryFee({{ $owner_id }}, {{$carts->first()->id}});
    }
});

function updateDeliveryFee(ownerId, cartId) {
    // Get the selected delivery type
    var deliveryType = document.querySelector('input[name="shipping_type_' + ownerId + '"]:checked').value;

    // Make an AJAX request to calculate the delivery fee
    $.ajax({
        url: "{{ url('calculate-delivery-fee') }}",
        method: 'POST',
        data: {
            owner_id: ownerId,
            delivery_type: deliveryType,
            cart_id: cartId,
            _token: '{{ csrf_token() }}' // Include CSRF token
        },
        success: function(response) {
            // Extract TotalAmount from the response
            var totalAmount = response.delivery_fee.original.delivery_fee[0].TotalAmount;

            // Check if totalAmount is a number
            if (!isNaN(totalAmount)) {
                // Update the delivery fee display
                document.getElementById('delivery-fee-amount').innerText = totalAmount.toFixed(2);
                // Emit an event or update a hidden input
               document.dispatchEvent(new CustomEvent('deliveryFeeUpdated', { detail: totalAmount }));
            } else {
                alert('Error: Invalid total amount received.');
            }
        },
        error: function() {
            alert('Error calculating delivery fee.');
        }
    });
}
</script>
