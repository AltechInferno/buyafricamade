@extends('frontend.layouts.app')

@section('content')
    <section class="mb-5" style="margin-top: 2rem;">
        <div class="container">
            <!-- Banner -->
            @php
                $lang = get_system_language()->code;
                $todays_deal_banner = get_setting('todays_deal_banner', null, $lang);
                $todays_deal_banner_small = get_setting('todays_deal_banner_small', null, $lang);
            @endphp
            @if ($todays_deal_banner != null || $todays_deal_banner_small != null)
                <div class="mb-4 overflow-hidden hov-scale-img d-none d-md-block">
                    <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" 
                        data-src="{{ uploaded_asset($todays_deal_banner) }}" 
                        alt="{{ env('APP_NAME') }} promo" class="lazyload img-fit h-100 has-transition" 
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                </div>
                <div class="mb-4 overflow-hidden hov-scale-img d-md-none">
                    <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" 
                        data-src="{{ $todays_deal_banner_small != null ? uploaded_asset($todays_deal_banner_small) : uploaded_asset($todays_deal_banner) }}" 
                        alt="{{ env('APP_NAME') }} promo" class="lazyload img-fit h-100 has-transition" 
                        onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder-rect.jpg') }}';">
                </div>
            @endif
            <!-- Products Section -->
            <div class="px-3">
                <div class="row row-cols-xxl-6 row-cols-xl-5 row-cols-lg-4 row-cols-md-3 row-cols-sm-2 row-cols-2 gutters-16 border-top border-left">
                    @foreach ($todays_deal_products as $key => $product)
                        <div class="col text-center border-right border-bottom has-transition hov-shadow-out z-1">
                            @include('frontend.'.get_setting('homepage_select').'.partials.product_box_1',['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
