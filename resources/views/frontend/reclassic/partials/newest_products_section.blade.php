@if (count($newest_products) > 0)
    @php
        $new_products_section_bg = get_setting('new_products_section_bg_color');
    @endphp
    <section class="mb-2 mb-md-3 mt-2 mt-md-3">
        <div class="container">
            <div class="p-3 p-md-2rem rounded-2 @if(get_setting('new_products_section_outline') == 1) border @endif" style="background: {{ $new_products_section_bg != null ? $new_products_section_bg : '#ffffff' }}; border-color: {{ get_setting('new_products_section_outline_color') }} !important; padding-bottom: 0 !important;">
                <!-- Top Section -->
                <div class="d-flex mb-2 mb-md-3 align-items-baseline justify-content-between">
                    <!-- Title -->
                    <h3 class="fs-16 fs-md-20 fw-700 mb-2 mb-sm-0">
                        <span class="">{{ translate('New Products') }}</span>
                    </h3>
                    <!-- Links -->
                    <div class="d-flex">
                        <a type="button" class="arrow-prev slide-arrow link-disable text-secondary mr-2" onclick="clickToSlide('slick-prev','section_newest')"><i class="las la-angle-left fs-20 fw-600"></i></a>
                        <a class="text-blue fs-10 fs-md-12 fw-700 hov-text-primary animate-underline-primary" href="{{ route('search',['sort_by'=>'newest']) }}">{{ translate('View All') }}</a>
                        <a type="button" class="arrow-next slide-arrow text-secondary ml-2" onclick="clickToSlide('slick-next','section_newest')"><i class="las la-angle-right fs-20 fw-600"></i></a>
                    </div>
                </div>
                <!-- Products Section -->
                <div class="px-sm-3">
                    <div class="aiz-carousel sm-gutters-16 arrow-none" data-items="6" data-xl-items="5" data-lg-items="4"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='false'>
                        @foreach ($newest_products as $key => $product)
                        <div class="carousel-box position-relative px-3 has-transition hov-animate-outline">
                            @include('frontend.'.get_setting('homepage_select').'.partials.product_box_1',['product' => $product])
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
