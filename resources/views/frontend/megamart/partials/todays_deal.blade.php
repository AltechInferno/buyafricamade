@if(count($todays_deal_products) > 0)
    <section  class="mb-2 mb-md-3 mt-2 mt-md-3">
        <div class="container">
            @php
                $lang = get_system_language()->code;
                $todays_deal_banner = get_setting('todays_deal_banner', null, $lang);
                $todays_deal_banner_small = get_setting('todays_deal_banner_small', null, $lang);
            @endphp
            <div class="row no-gutters">
                <!-- Banner -->
                @if ($todays_deal_banner != null || $todays_deal_banner_small != null)
                    <div class="col-xl-5">
                        <div class="overflow-hidden h-100 d-none d-md-block">
                            <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" 
                                data-src="{{ uploaded_asset($todays_deal_banner) }}" 
                                alt="{{ env('APP_NAME') }} promo" class="lazyload img-fit h-100 has-transition" 
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                        </div>
                        <div class="overflow-hidden h-100 d-md-none">
                            <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" 
                                data-src="{{ $todays_deal_banner_small != null ? uploaded_asset($todays_deal_banner_small) : uploaded_asset($todays_deal_banner) }}" 
                                alt="{{ env('APP_NAME') }} promo" class="lazyload img-fit h-100 has-transition" 
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                        </div>
                    </div>
                @endif
                <!-- Products -->
                @php
                    $todays_deal_banner_text_color =  ((get_setting('todays_deal_banner_text_color') == 'light') ||  (get_setting('todays_deal_banner_text_color') == null)) ? 'text-white' : 'text-dark';
                    $col_val = $todays_deal_banner != null ? 'col-xl-7' : 'col-xl-12';
                    $xxl_items = $todays_deal_banner != null ? 5 : 7;
                    $xl_items = $todays_deal_banner != null ? 4 : 6;
                @endphp
                <div class="{{ $col_val }}" style="background-color: {{ get_setting('todays_deal_bg_color', '#3d4666') }}">
                    <div class="d-flex flex-wrap align-items-baseline justify-content-between px-4 px-xl-5 pt-4">
                        <h3 class="fs-16 fs-md-20 fw-700 mb-2 mb-sm-0">{{ translate("Today’s Deal") }}</h3>
                        <a href="{{ route('todays-deal') }}" class="fs-12 fw-700 {{ $todays_deal_banner_text_color }} has-transition hov-text-secondary-base">{{ translate('View All') }}</a>
                    </div>
                    <div class="c-scrollbar-light overflow-hidden px-4 px-md-5 pb-3 pt-3 pt-md-3 pb-md-5">
                        <div class="h-100 d-flex flex-column justify-content-center">
                            <div class="todays-deal aiz-carousel" data-items="{{ $xxl_items }}" data-xxl-items="{{ $xxl_items }}" data-xl-items="{{ $xxl_items }}" data-lg-items="5" data-md-items="4" data-sm-items="3" data-xs-items="2" data-arrows="true" data-dots="false" data-autoplay="true" data-infinite="true">
                                @foreach ($todays_deal_products as $key => $product)
                                    <div class="carousel-box h-100 px-3 px-lg-0">
                                        <a href="{{ route('product', $product->slug) }}" class="h-100 overflow-hidden hov-scale-img mx-auto" title="{{  $product->getTranslation('name')  }}">
                                            <!-- Image -->
                                            <div class="img h-80px w-80px rounded-content overflow-hidden mx-auto">
                                                <img class="lazyload img-fit m-auto has-transition"
                                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                                    data-src="{{ get_image($product->thumbnail) }}"
                                                    alt="{{ $product->getTranslation('name') }}"
                                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                            </div>
                                            <!-- Price -->
                                            <div class="fs-14 mt-3 text-center">
                                                <span class="d-block {{ $todays_deal_banner_text_color }} fw-700">{{ home_discounted_base_price($product) }}</span>
                                                @if(home_base_price($product) != home_discounted_base_price($product))
                                                    <del class="d-block text-secondary fw-400">{{ home_base_price($product) }}</del>
                                                @endif
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endif