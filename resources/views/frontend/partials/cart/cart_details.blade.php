<div class="container">
    @php
        $cart_count = count($carts);
        $active_carts = $cart_count > 0 ? $carts->toQuery()->active()->get() : [];
    @endphp
    @if( $cart_count > 0 )
        <div class="row">
            <div class="col-lg-8">
                @if(auth()->check())
                    @php
                        $welcomeCoupon = ifUserHasWelcomeCouponAndNotUsed();
                    @endphp
                    @if($welcomeCoupon)
                        <div class="alert alert-primary align-items-center border d-flex flex-wrap justify-content-between rounded-0" style="border-color: #3490F3 !important;">
                            @php
                                $discount = $welcomeCoupon->discount_type == 'amount' ? single_price($welcomeCoupon->discount) : $welcomeCoupon->discount.'%';
                            @endphp
                            <div class="fw-400 fs-14" style="color: #3490F3 !important;">
                                {{ translate('Welcome Coupon') }} <strong>{{ $discount }}</strong> {{ translate('Discount on your Purchase Within') }} <strong>{{ $welcomeCoupon->validation_days }}</strong> {{ translate('days of Registration') }}
                            </div>
                            <button class="btn btn-sm mt-3 mt-lg-0 rounded-4" onclick="copyCouponCode('{{ $welcomeCoupon->coupon_code }}')" style="background-color: #3490F3; color: white;" >{{ translate('Copy coupon Code') }}</button>
                        </div>
                    @endif
                @endif
                <div class="bg-white p-3 p-lg-4 text-left">
                    <div class="mb-4">
                        <div class="form-group mb-2 border-bottom">
                            <div class="aiz-checkbox-inline mb-3">
                                <label class="aiz-checkbox">
                                    <input type="checkbox" class="check-all" @if(count($active_carts) == $cart_count) checked @endif>
                                    <span class="fs-14 text-secondary ml-3">{{ translate('Select All') }} ({{ $cart_count }})</span>
                                    <span class="aiz-square-check"></span>
                                </label>
                            </div>
                        </div>
                        <!-- Cart Items -->
                        <ul class="list-group list-group-flush">
                            @php
                                $total = 0;
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
                            @endphp

                            <!-- Inhouse Products -->
                            @if (!empty($admin_products))
                                @php
                                    $all_admin_products = true;
                                    if(count($admin_products) != count($carts->toQuery()->active()->whereIn('product_id', $admin_products)->get())){
                                        $all_admin_products = false;
                                    }
                                @endphp
                                <div class="pt-3 px-0">
                                    <div class="aiz-checkbox-inline">
                                        <label class="aiz-checkbox d-block">
                                            <input type="checkbox" class="check-one check-seller" value="admin" @if($all_admin_products) checked @endif>
                                            <span class="fs-16 fw-700 text-dark ml-3 pb-3 d-block border-left-0 border-top-0 border-right-0 border-bottom border-dashed">
                                                {{ translate('Inhouse Products') }} ({{ count($admin_products) }})
                                            </span>
                                            <span class="aiz-square-check"></span>
                                        </label>
                                    </div>
                                </div>
                                @foreach ($admin_products as $key => $product_id)
                                    @php
                                        $product = get_single_product($product_id);
                                        $cartItem = $carts->toQuery()->where('product_id', $product_id)->first();
                                        $product_stock = $product->stocks->where('variant', $cartItem->variation)->first();
                                        $total = $total + cart_product_price($cartItem, $product, false) * $cartItem->quantity;
                                    @endphp
                                    <li class="list-group-item px-0 border-md-0">
                                        <div class="row gutters-5 align-items-center">
                                            <!-- select -->
                                            <div class="col-auto">
                                                <div class="aiz-checkbox pl-0">
                                                    <label class="aiz-checkbox">
                                                        <input type="checkbox" class="check-one check-one-admin" name="id[]" value="{{$product_id}}" @if($cartItem->status == 1) checked @endif>
                                                        <span class="aiz-square-check"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- Product Image & name -->
                                            <div class="col-md-5 col-10 d-flex align-items-center mb-2 mb-md-0">
                                                <span class="mr-2 ml-0">
                                                    <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                        class="img-fit size-64px"
                                                        alt="{{ $product->getTranslation('name')  }}"
                                                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                </span>
                                                <span>
                                                    <span class="fs-14 fw-400 text-dark text-truncate-2 mb-2">{{ $product->getTranslation('name') }}</span>
                                                    @if ($admin_product_variation[$key] != '')
                                                        <span class="fs-12 text-secondary">{{ translate('Variation') }}: {{ $admin_product_variation[$key] }}</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <!-- Price & Tax -->
                                            <div class="col-md col-4 ml-4 ml-sm-0 my-3 my-md-0 d-flex flex-column ml-sm-5 ml-md-0">
                                                <span class="fs-12 text-secondary">{{ translate('Price')}}</span>
                                                <span class="fw-700 fs-14 mb-2">{{ cart_product_price($cartItem, $product, true, false) }}</span>
                                                <span>
                                                    <span class="opacity-90 fs-12">{{ translate('Tax')}}: {{ cart_product_tax($cartItem, $product) }}</span>
                                                </span>
                                            </div>
                                            <!-- Quantity & Total -->
                                            <div class="col-xl-4 col-md-3 col d-flex flex-column flex-xl-row justify-content-xl-between align-items-xl-center">
                                                <!-- Quantity -->
                                                <div>
                                                    @if ($product->digital != 1 && $product->auction_product == 0)
                                                        <div class="d-flex flex-xl-column flex-xxl-row align-items-center aiz-plus-minus mr-0 ml-0" style="width: max-content !important;">
                                                            <button
                                                                class="btn col-auto btn-icon btn-sm btn-light rounded-0"
                                                                type="button" data-type="plus"
                                                                data-field="quantity[{{ $cartItem->id }}]">
                                                                <i class="las la-plus"></i>
                                                            </button>
                                                            <input type="number" name="quantity[{{ $cartItem->id }}]"
                                                                class="col border-0 text-center px-0 fs-14 input-number"
                                                                placeholder="1" value="{{ $cartItem['quantity'] }}"
                                                                min="{{ $product->min_qty }}"
                                                                max="{{ $product_stock->qty }}"
                                                                onchange="updateQuantity({{ $cartItem->id }}, this)" style="min-width: 45px;">
                                                            <button
                                                                class="btn col-auto btn-icon btn-sm btn-light rounded-0"
                                                                type="button" data-type="minus"
                                                                data-field="quantity[{{ $cartItem->id }}]">
                                                                <i class="las la-minus"></i>
                                                            </button>
                                                        </div>
                                                    @elseif($product->auction_product == 1)
                                                        <span class="fw-700 fs-14">1</span>
                                                    @endif
                                                </div>
                                                <!-- Total -->
                                                <div class="mr-2 mt-2 mt-xl-0">
                                                    <span class="fw-700 fs-14 text-primary">{{ single_price(cart_product_price($cartItem, $product, false) * $cartItem->quantity) }}</span>
                                                </div>
                                            </div>
                                            <!-- Remove From Cart -->
                                            <div class="col-auto text-right">
                                                <a href="javascript:void(0)" onclick="removeFromCartView(event, {{ $cartItem->id }})" class="btn btn-icon btn-sm bg-white hov-svg-danger" title="{{ translate('Remove') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12.27" height="16" viewBox="0 0 12.27 16">
                                                        <g id="Group_23970" data-name="Group 23970" transform="translate(-1332 -420)">
                                                          <path id="Path_28714" data-name="Path 28714" d="M17.9,9.037l-.258,7.8a2.569,2.569,0,0,1-2.577,2.485h-4.9A2.569,2.569,0,0,1,7.587,16.84l-.258-7.8a.645.645,0,0,1,1.289-.043l.258,7.8a1.289,1.289,0,0,0,1.289,1.239h4.9a1.289,1.289,0,0,0,1.289-1.241l.258-7.8a.645.645,0,0,1,1.289.043Zm.852-2.6a.644.644,0,0,1-.644.644H7.122a.644.644,0,1,1,0-1.289h2a.822.822,0,0,0,.82-.74,1.927,1.927,0,0,1,1.922-1.736h1.5a1.927,1.927,0,0,1,1.922,1.736.822.822,0,0,0,.82.74h2a.644.644,0,0,1,.644.644ZM11.058,5.8h3.11A2.126,2.126,0,0,1,14,5.189a.644.644,0,0,0-.64-.58h-1.5a.644.644,0,0,0-.64.58,2.126,2.126,0,0,1-.165.608Zm.649,9.761V10.072a.644.644,0,0,0-1.289,0v5.488a.644.644,0,0,0,1.289,0Zm3.1,0V10.072a.644.644,0,1,0-1.289,0v5.488a.644.644,0,1,0,1.289,0Z" transform="translate(1325.522 416.678)" fill="#9d9da6"/>
                                                        </g>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @endif

                            <!-- Seller Products -->
                            @if (!empty($seller_products))
                                @foreach ($seller_products as $key => $seller_product)
                                    @php
                                        $all_seller_products = true;
                                        if(count($seller_product) != count($carts->toQuery()->active()->whereIn('product_id', $seller_product)->get())){
                                            $all_seller_products = false;
                                        }
                                    @endphp
                                    <div class="pt-3 px-0">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox d-block">
                                                <input type="checkbox" class="check-one check-seller" value="seller-{{ $key }}"  @if($all_seller_products) checked @endif>
                                                <span class="fs-16 fw-700 text-dark ml-3 pb-3 d-block border-left-0 border-top-0 border-right-0 border-bottom border-dashed">
                                                    {{ get_shop_by_user_id($key)->name }} {{ translate('Products') }} ({{ count($seller_product) }})
                                                </span>
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                    @foreach ($seller_product as $key2 => $product_id)
                                        @php
                                            $product = get_single_product($product_id);
                                            $cartItem = $carts->toQuery()->where('product_id', $product_id)->first();
                                            $product_stock = $product->stocks->where('variant', $cartItem->variation)->first();
                                            $total = $total + cart_product_price($cartItem, $product, false) * $cartItem->quantity;
                                        @endphp
                                        <li class="list-group-item px-0 border-md-0">
                                            <div class="row gutters-5 align-items-center">
                                                <!-- select -->
                                                <div class="col-auto">
                                                    <div class="aiz-checkbox pl-0">
                                                        <label class="aiz-checkbox">
                                                            <input type="checkbox" class="check-one check-one-seller-{{ $key }}" name="id[]" value="{{$product_id}}" @if($cartItem->status == 1) checked @endif>
                                                            <span class="aiz-square-check"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                                <!-- Product Image & name -->
                                                <div class="col-md-5 col-10 d-flex align-items-center mb-2 mb-md-0">
                                                    <span class="mr-2 ml-0">
                                                        <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                            class="img-fit size-64px"
                                                            alt="{{ $product->getTranslation('name')  }}"
                                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                    </span>
                                                    <span>
                                                        <span class="fs-14 fw-400 text-dark text-truncate-2 mb-2">{{ $product->getTranslation('name') }}</span>
                                                        @if ($seller_product_variation[$key2] != '')
                                                            <span class="fs-12 text-secondary">{{ translate('Variation') }}: {{ $seller_product_variation[$key2] }}</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <!-- Price & Tax -->
                                                <div class="col-md col-4 ml-4 ml-sm-0 my-3 my-md-0 d-flex flex-column ml-sm-5 ml-md-0">
                                                    <span class="fs-12 text-secondary">{{ translate('Price')}}</span>
                                                    <span class="fw-700 fs-14 mb-2">{{ cart_product_price($cartItem, $product, true, false) }}</span>
                                                    <span>
                                                        <span class="opacity-90 fs-12">{{ translate('Tax')}}: {{ cart_product_tax($cartItem, $product) }}</span>
                                                    </span>
                                                </div>
                                                <!-- Quantity & Total -->
                                                <div class="col-xl-4 col-md-3 col d-flex flex-column flex-xl-row justify-content-xl-between align-items-xl-center">
                                                    <!-- Quantity -->
                                                    <div>
                                                        @if ($product->digital != 1 && $product->auction_product == 0)
                                                            <div class="d-flex flex-xl-column flex-xxl-row align-items-center aiz-plus-minus mr-0 ml-0" style="width: max-content !important;">
                                                                <button
                                                                    class="btn col-auto btn-icon btn-sm btn-light rounded-0"
                                                                    type="button" data-type="plus"
                                                                    data-field="quantity[{{ $cartItem->id }}]">
                                                                    <i class="las la-plus"></i>
                                                                </button>
                                                                <input type="number" name="quantity[{{ $cartItem->id }}]"
                                                                    class="col border-0 text-center px-0 fs-14 input-number"
                                                                    placeholder="1" value="{{ $cartItem['quantity'] }}"
                                                                    min="{{ $product->min_qty }}"
                                                                    max="{{ $product_stock->qty }}"
                                                                    onchange="updateQuantity({{ $cartItem->id }}, this)" style="min-width: 45px;">
                                                                <button
                                                                    class="btn col-auto btn-icon btn-sm btn-light rounded-0"
                                                                    type="button" data-type="minus"
                                                                    data-field="quantity[{{ $cartItem->id }}]">
                                                                    <i class="las la-minus"></i>
                                                                </button>
                                                            </div>
                                                        @elseif($product->auction_product == 1)
                                                            <span class="fw-700 fs-14">1</span>
                                                        @endif
                                                    </div>
                                                    <!-- Total -->
                                                    <div class="mr-2 mt-2 mt-xl-0">
                                                        <span class="fw-700 fs-14 text-primary">{{ single_price(cart_product_price($cartItem, $product, false) * $cartItem->quantity) }}</span>
                                                    </div>
                                                </div>
                                                <!-- Remove From Cart -->
                                                <div class="col-auto text-right">
                                                    <a href="javascript:void(0)" onclick="removeFromCartView(event, {{ $cartItem->id }})" class="btn btn-icon btn-sm bg-white hov-svg-danger" title="{{ translate('Remove') }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12.27" height="16" viewBox="0 0 12.27 16">
                                                            <g id="Group_23970" data-name="Group 23970" transform="translate(-1332 -420)">
                                                              <path id="Path_28714" data-name="Path 28714" d="M17.9,9.037l-.258,7.8a2.569,2.569,0,0,1-2.577,2.485h-4.9A2.569,2.569,0,0,1,7.587,16.84l-.258-7.8a.645.645,0,0,1,1.289-.043l.258,7.8a1.289,1.289,0,0,0,1.289,1.239h4.9a1.289,1.289,0,0,0,1.289-1.241l.258-7.8a.645.645,0,0,1,1.289.043Zm.852-2.6a.644.644,0,0,1-.644.644H7.122a.644.644,0,1,1,0-1.289h2a.822.822,0,0,0,.82-.74,1.927,1.927,0,0,1,1.922-1.736h1.5a1.927,1.927,0,0,1,1.922,1.736.822.822,0,0,0,.82.74h2a.644.644,0,0,1,.644.644ZM11.058,5.8h3.11A2.126,2.126,0,0,1,14,5.189a.644.644,0,0,0-.64-.58h-1.5a.644.644,0,0,0-.64.58,2.126,2.126,0,0,1-.165.608Zm.649,9.761V10.072a.644.644,0,0,0-1.289,0v5.488a.644.644,0,0,0,1.289,0Zm3.1,0V10.072a.644.644,0,1,0-1.289,0v5.488a.644.644,0,1,0,1.289,0Z" transform="translate(1325.522 416.678)" fill="#9d9da6"/>
                                                            </g>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4 mt-lg-0 mt-4" id="cart_summary">
                @include('frontend.partials.cart.cart_summary', ['proceed' => 1, 'carts' => $active_carts])
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="border bg-white p-4">
                    <!-- Empty cart -->
                    <div class="text-center p-3">
                        <i class="las la-frown la-3x opacity-60 mb-3"></i>
                        <h3 class="h4 fw-700">{{translate('Your Cart is empty')}}</h3>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
