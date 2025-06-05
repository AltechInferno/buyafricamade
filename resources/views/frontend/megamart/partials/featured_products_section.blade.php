@if (count(get_featured_products()) > 0)
    @php 
        $lang = get_system_language()->code;
        $homeBanner1Images = get_setting('home_banner1_images', null, $lang);
        $xxl_items = 6;
        $xl_items = 5;
        $lg_items = 4;
        $md_items = 3;
        if($homeBanner1Images != null){
            $xxl_items = 5;
            $xl_items = 3.5;
            $lg_items = 3;
            $md_items = 1.5;
        }
    @endphp
    <section class="mb-2 mb-md-3 mt-2 mt-md-3">
        <div class="container">
            <div class="row gutters-15">
                <div class="col" id="section_featured_div">
                    <div class="border">
                        <!-- Top Section -->
                        <div class="d-flex px-4 py-3 align-items-baseline justify-content-between">
                            <!-- Title -->
                            <h3 class="fs-16 fs-md-20 fw-700 mb-2 mb-sm-0">
                                <span class="">{{ translate('Featured Products') }}</span>
                            </h3>
                            <!-- Links -->
                            <div class="d-flex">
                                <a type="button" class="arrow-prev slide-arrow link-disable text-secondary mr-2" onclick="clickToSlide('slick-prev','section_featured_div')"><i class="las la-angle-left fs-20 fw-600"></i></a>
                                <a type="button" class="arrow-next slide-arrow text-secondary ml-2" onclick="clickToSlide('slick-next','section_featured_div')"><i class="las la-angle-right fs-20 fw-600"></i></a>
                            </div>
                        </div>
                        <!-- Products Section -->
                        <div class="px-xl-1">
                            <div class="aiz-carousel arrow-none" data-items="{{ $xxl_items }}" data-xl-items="{{ $xl_items }}" data-lg-items="{{ $lg_items }}"  data-md-items="{{ $md_items }}" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='false'>
                                @foreach (get_featured_products() as $key => $product)
                                <div class="carousel-box position-relative px-0 has-transition hov-animate-outline">
                                    <div class="px-3">
                                        @include('frontend.'.get_setting('homepage_select').'.partials.product_box_1',['product' => $product])
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Banner section 1 -->
                @if ($homeBanner1Images != null)
                    <div class="col-xxl-3 col-lg-4 col-md-6 d-none d-md-block">
                        @php
                            $banner_1_imags = json_decode($homeBanner1Images);
                            $home_banner1_links = get_setting('home_banner1_links', null, $lang);
                        @endphp
                        <div class="aiz-carousel overflow-hidden arrow-inactive-none arrow-dark arrow-x-0"
                            data-items="1" data-arrows="true" data-dots="false" data-autoplay="true">
                            @foreach ($banner_1_imags as $key => $value)
                                <div class="carousel-box overflow-hidden hov-scale-img">
                                    <a href="{{ isset(json_decode($home_banner1_links, true)[$key]) ? json_decode($home_banner1_links, true)[$key] : '' }}"
                                        class="d-block text-reset overflow-hidden" style="height: 370px;">
                                        <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}"
                                            data-src="{{ uploaded_asset($value) }}" alt="{{ env('APP_NAME') }} promo"
                                            class="img-fit h-100 lazyload has-transition"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>   
@endif