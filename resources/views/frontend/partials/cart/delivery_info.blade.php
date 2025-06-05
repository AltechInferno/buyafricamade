@php
    $admin_products = array();
    $seller_products = array();
    $admin_product_variation = array();
    $seller_product_variation = array();
    foreach ($carts as $key => $cartItem){
        $product = get_single_product($cartItem['product_id']);

        if($product->added_by == 'admin'){
            array_push($admin_products, $cartItem['product_id']);
            $admin_product_variation[] = $cartItem['variation'];
        }
        else{
            $product_ids = array();
            if(isset($seller_products[$product->user_id])){
                $product_ids = $seller_products[$product->user_id];
            }
            array_push($product_ids, $cartItem['product_id']);
            $seller_products[$product->user_id] = $product_ids;
            $seller_product_variation[] = $cartItem['variation'];
        }
    }

    $pickup_point_list = array();
    if (get_setting('pickup_point') == 1) {
        $pickup_point_list = get_all_pickup_points();
    }
@endphp

<!-- Inhouse Products -->
@if (!empty($admin_products))
    <div class="card mb-3 border-left-0 border-top-0 border-right-0 border-bottom rounded-0 shadow-none">
        <div class="card-header py-3 px-0 border-left-0 border-top-0 border-right-0 border-bottom border-dashed">
            <h5 class="fs-16 fw-700 text-dark mb-0">{{ get_setting('site_name') }} {{ translate('Inhouse Products') }} ({{ sprintf("%02d", count($admin_products)) }})</h5>
        </div>
        <div class="card-body p-0">
            @include('frontend.partials.cart.delivery_info_details', ['products' => $admin_products, 'product_variation' => $admin_product_variation, 'owner_id' => get_admin()->id ])
        </div>
    </div>
@endif

<!-- Seller Products -->
@if (!empty($seller_products))
    @foreach ($seller_products as $key => $seller_product)
        <div class="card @if($loop->last) mb-0 @else mb-3 @endif border-left-0 border-top-0 border-right-0 @if($loop->last) border-bottom-0 @else border-bottom @endif rounded-0 shadow-none">
            <div class="card-header py-3 px-0 border-left-0 border-top-0 border-right-0 border-bottom border-dashed">
                <h5 class="fs-16 fw-700 text-dark mb-0">{{ get_shop_by_user_id($key)->name }} {{ translate('Products') }} ({{ sprintf("%02d", count($seller_product)) }})</h5>
            </div>
            <div class="card-body p-0">
                @include('frontend.partials.cart.delivery_info_details', ['products' => $seller_product, 'product_variation' => $seller_product_variation, 'owner_id' => $key ])
            </div>
        </div>
    @endforeach
@endif
