@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class=" align-items-center">
            <h1 class="h3">{{ translate('Earning Report') }}</h1>
        </div>
    </div>

    <div class="row gutters-16">

        <!-- Customer, Products, Category, Brands -->
        <div class="col-lg-12">
            <div class="row gutters-16">

                <!-- Sale Report -->
                <div class="col-sm-3">
                    <div class="dashboard-box bg-white h-220px mb-2rem overflow-hidden" style="min-height: 370px">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div class="">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h1 class="fs-30 fw-600 text-dark mb-1">
                                            {{ single_price($total_sales_alltime) }}
                                        </h1>
                                        <h3 class="fs-13 fw-600 text-secondary mb-0">{{ translate('Total Sales Alltime') }}</h3>
                                    </div>
                                    <div class="mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                            viewBox="0 0 32 32">
                                            <path id="Path_41567" data-name="Path 41567"
                                                d="M21,13.75a1.25,1.25,0,0,0,2.5,0,7.508,7.508,0,0,0-4.068-6.667,4.375,4.375,0,1,0-6.865,0A7.508,7.508,0,0,0,8.5,13.75a1.25,1.25,0,0,0,2.5,0,5,5,0,0,1,10,0ZM14.125,4.375A1.875,1.875,0,1,1,16,6.25,1.877,1.877,0,0,1,14.125,4.375ZM10.932,24.083a4.375,4.375,0,1,0-6.865,0A7.508,7.508,0,0,0,0,30.75a1.25,1.25,0,0,0,2.5,0,5,5,0,0,1,10,0,1.25,1.25,0,0,0,2.5,0A7.508,7.508,0,0,0,10.932,24.083ZM5.625,21.375A1.875,1.875,0,1,1,7.5,23.25,1.877,1.877,0,0,1,5.625,21.375Zm22.307,2.708a4.375,4.375,0,1,0-6.865,0A7.508,7.508,0,0,0,17,30.75a1.25,1.25,0,0,0,2.5,0,5,5,0,0,1,10,0,1.25,1.25,0,0,0,2.5,0A7.508,7.508,0,0,0,27.932,24.083Zm-5.307-2.708A1.875,1.875,0,1,1,24.5,23.25,1.877,1.877,0,0,1,22.625,21.375Zm0,0"
                                                fill="#d5d6db" />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="d-flex align-items-center justify-content-between p-3 rounded-2 bg-primary text-white mt-4">
                                    <h3 class="fs-13 fw-600 mb-0">
                                        {{ translate('Sales this month') }}
                                    </h3>
                                    <h3 class="fs-13 fw-600 mb-0">
                                        {{ single_price($sales_this_month) }}
                                    </h3>
                                </div>
                            </div>

                            <div>
                                <canvas id="graph-1" class="w-100" height="140"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payout Report -->
                <div class="col-sm-3">
                    <div class="dashboard-box bg-white h-220px mb-2rem overflow-hidden" style="min-height: 370px">
                        <div class="d-flex flex-column justify-content-between h-100">
                            <div class="">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h1 class="fs-30 fw-600 text-dark mb-1">
                                            {{ single_price($total_payouts) }}
                                        </h1>
                                        <h3 class="fs-13 fw-600 text-secondary mb-0">{{ translate('Payouts') }}</h3>
                                    </div>
                                    <div class="mt-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                            viewBox="0 0 32 32">
                                            <path id="Path_41567" data-name="Path 41567"
                                                d="M21,13.75a1.25,1.25,0,0,0,2.5,0,7.508,7.508,0,0,0-4.068-6.667,4.375,4.375,0,1,0-6.865,0A7.508,7.508,0,0,0,8.5,13.75a1.25,1.25,0,0,0,2.5,0,5,5,0,0,1,10,0ZM14.125,4.375A1.875,1.875,0,1,1,16,6.25,1.877,1.877,0,0,1,14.125,4.375ZM10.932,24.083a4.375,4.375,0,1,0-6.865,0A7.508,7.508,0,0,0,0,30.75a1.25,1.25,0,0,0,2.5,0,5,5,0,0,1,10,0,1.25,1.25,0,0,0,2.5,0A7.508,7.508,0,0,0,10.932,24.083ZM5.625,21.375A1.875,1.875,0,1,1,7.5,23.25,1.877,1.877,0,0,1,5.625,21.375Zm22.307,2.708a4.375,4.375,0,1,0-6.865,0A7.508,7.508,0,0,0,17,30.75a1.25,1.25,0,0,0,2.5,0,5,5,0,0,1,10,0,1.25,1.25,0,0,0,2.5,0A7.508,7.508,0,0,0,27.932,24.083Zm-5.307-2.708A1.875,1.875,0,1,1,24.5,23.25,1.877,1.877,0,0,1,22.625,21.375Zm0,0"
                                                fill="#d5d6db" />
                                        </svg>
                                    </div>
                                </div>
                                <div
                                    class="d-flex align-items-center justify-content-between p-3 rounded-2 bg-danger text-white mt-4">
                                    <h3 class="fs-13 fw-600 mb-0">
                                        {{ translate('Payouts this month') }}
                                    </h3>
                                    <h3 class="fs-13 fw-600 mb-0">
                                        {{ single_price($payout_this_month) }}
                                    </h3>
                                </div>
                            </div>

                            <div>
                                <canvas id="graph-2" class="w-100" height="140"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Category -->
                <div class="col-sm-3">
                    <div class="dashboard-box bg-white h-220px mb-2rem overflow-hidden" style="min-height: 370px">
                        <div class="h-100">
                            <div class="d-flex justify-content-between mb-4">
                                <div>
                                    <h1 class="fs-30 fw-600 text-dark mb-1">{{ $total_categories }}</h1>
                                    <h3 class="fs-13 fw-600 text-secondary mb-0">{{ translate('Total Category') }}</h3>
                                </div>
                                <div class="mt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                                        viewBox="0 0 32 32">
                                        <path id="_137b5e1009c61a91dc419a2998502736"
                                            data-name="137b5e1009c61a91dc419a2998502736"
                                            d="M27.144,17.266A4.922,4.922,0,0,1,32,22.207h0v4.836A4.937,4.937,0,0,1,27.144,32H22.407a4.922,4.922,0,0,1-4.841-4.957h0V22.207a4.892,4.892,0,0,1,4.841-4.942h4.737Zm-20.343,0a1.3,1.3,0,0,1,1.247.619,1.358,1.358,0,0,1,0,1.415,1.3,1.3,0,0,1-1.247.619H4.856A2.281,2.281,0,0,0,2.6,22.208h0v4.775a2.326,2.326,0,0,0,2.257,2.289H9.622a2.219,2.219,0,0,0,1.6-.665,2.313,2.313,0,0,0,.662-1.624h0v-7.17l-.02-.178a1.342,1.342,0,0,1,.606-1.19,1.285,1.285,0,0,1,1.462.043,1.348,1.348,0,0,1,.506,1.4h0v7.14a4.907,4.907,0,0,1-4.856,4.957H4.856A5.012,5.012,0,0,1,0,27.028H0v-4.82a4.994,4.994,0,0,1,1.423-3.5,4.791,4.791,0,0,1,3.433-1.442H6.8Zm20.343,2.653H22.407a2.266,2.266,0,0,0-2.242,2.289h0v4.836a2.3,2.3,0,0,0,.652,1.623,2.2,2.2,0,0,0,1.59.666h4.737a2.2,2.2,0,0,0,1.59-.666,2.3,2.3,0,0,0,.652-1.623h0V22.207a2.313,2.313,0,0,0-.657-1.619,2.219,2.219,0,0,0-1.585-.67ZM27.144,0a5.013,5.013,0,0,1,4.841,4.957h0v4.82a5,5,0,0,1-1.376,3.512A4.794,4.794,0,0,1,27.2,14.78h-1.96a1.337,1.337,0,0,1,0-2.653h1.9a2.235,2.235,0,0,0,1.6-.691,2.33,2.33,0,0,0,.645-1.644h0V4.957a2.3,2.3,0,0,0-2.242-2.289H22.407a2.266,2.266,0,0,0-2.242,2.289h0v7.231l-.015.166a1.33,1.33,0,0,1-1.321,1.137,1.28,1.28,0,0,1-.91-.413,1.335,1.335,0,0,1-.352-.951h0V4.957a5,5,0,0,1,1.413-3.5A4.791,4.791,0,0,1,22.407,0h4.737ZM9.593,0a4.922,4.922,0,0,1,4.856,4.957h0V9.793a4.994,4.994,0,0,1-1.423,3.5,4.791,4.791,0,0,1-3.433,1.442H4.856A4.922,4.922,0,0,1,0,9.793H0V4.957A4.937,4.937,0,0,1,4.856,0H9.593Zm0,2.668H4.856a2.218,2.218,0,0,0-1.614.654,2.313,2.313,0,0,0-.672,1.635h0V9.793a2.314,2.314,0,0,0,.656,1.664,2.218,2.218,0,0,0,1.63.67H9.593a2.235,2.235,0,0,0,1.6-.691,2.33,2.33,0,0,0,.645-1.644h0V4.957A2.281,2.281,0,0,0,9.593,2.668Z"
                                            fill="#d5d6dc" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                @foreach ($top_categories as $key => $top_category)
                                    <div class="d-flex justify-content-between mb-1">
                                        @php
                                            $badge = 'badge-danger';
                                            if ($key == 1) {
                                                $badge = 'badge-warning';
                                            }
                                            if ($key == 2) {
                                                $badge = 'badge-primary';
                                            }
                                            $lang = App::getLocale();
                                            $category = App\Models\CategoryTranslation::where('category_id', $top_category->id)
                                                ->where('lang', $lang)
                                                ->first();
                                        @endphp
                                        <h3 class="fs-13 opacity-60 mb-0 d-flex align-items-center text-truncate mr-2"
                                            title="{{ $category ? $category->name : translate('Not Found') }}">
                                            <span class="badge badge-sm badge-dot {{ $badge }} mr-2"
                                                style="height:4px !important; width:20px !important;"></span>
                                            {{ $category ? $category->name : translate('Not Found') }}
                                        </h3>
                                        <h3 class="fs-13 fw-600 mb-0">
                                            {{ single_price($top_category->total) }}
                                        </h3>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Brands -->
                <div class="col-sm-3">
                    <div class="dashboard-box bg-white h-220px mb-2rem overflow-hidden" style="min-height: 370px">
                        <div class="h-100">
                            <div class="d-flex justify-content-between mb-4">
                                <div>
                                    <h1 class="fs-30 fw-600 text-dark mb-1">{{ $total_brands }}</h1>
                                    <h3 class="fs-13 fw-600 text-secondary mb-0">{{ translate('Total Brands') }}</h3>
                                </div>
                                <div class="mt-2">
                                    <svg id="Layer_51" data-name="Layer 51" xmlns="http://www.w3.org/2000/svg"
                                        width="31.994" height="32" viewBox="0 0 31.994 32">
                                        <path id="Path_41568" data-name="Path 41568"
                                            d="M22.534,33.9a3.963,3.963,0,0,1-2.813-1.139L3.175,16.112A4.02,4.02,0,0,1,2.037,12.49L3.175,6.854A3.952,3.952,0,0,1,6.056,3.768l6.377-1.754a4.1,4.1,0,0,1,3.906,1.139L32.783,19.6a4.031,4.031,0,0,1,0,5.694l-7.368,7.47A3.986,3.986,0,0,1,22.534,33.9Zm8.677-12.686L14.722,4.724a1.788,1.788,0,0,0-1.3-.524,1.492,1.492,0,0,0-.444.057L6.592,5.965A1.72,1.72,0,0,0,5.339,7.286L4.257,12.912a1.788,1.788,0,0,0,.49,1.628L21.327,31.1a1.765,1.765,0,0,0,1.207.524,1.663,1.663,0,0,0,1.207-.5l7.5-7.47A1.742,1.742,0,0,0,31.212,21.213Z"
                                            transform="translate(-1.966 -1.901)" fill="#d5d6dc" />
                                        <path id="Path_41569" data-name="Path 41569"
                                            d="M20.246,26A1.139,1.139,0,0,1,18.629,24.4L24.824,18.2a1.139,1.139,0,1,1,1.606,1.617Zm-7.983-9.953a4.316,4.316,0,1,1,4.293-4.339A4.339,4.339,0,0,1,12.263,16.052Zm1.355-6.229a2,2,0,0,0-1.435-.6,1.947,1.947,0,0,0-1.446.569,1.981,1.981,0,0,0-.581,1.412,2.129,2.129,0,0,0,.649,1.435,2.016,2.016,0,0,0,2.847,0,1.925,1.925,0,0,0,.569-1.412,2.027,2.027,0,0,0-.6-1.4Z"
                                            transform="translate(-1.557 -1.135)" fill="#d5d6dc" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="fs-13 fw-600 text-secondary mb-2">{{ translate('Top Brands') }}</h3>
                                @foreach ($top_brands as $key => $top_brand)
                                    <div class="d-flex justify-content-between mb-0">
                                        @php
                                            $badge = 'badge-success';
                                            if ($key == 1) {
                                                $badge = 'badge-primary';
                                            }
                                            if ($key == 2) {
                                                $badge = 'badge-info';
                                            }
                                            $lang = App::getLocale();
                                            $brand = App\Models\BrandTranslation::where('brand_id', $top_brand->id)
                                                ->where('lang', $lang)
                                                ->first();
                                        @endphp
                                        <h3 class="fs-13 fw-600 mb-0 text-truncate mr-2"
                                            title="{{ $brand ? $brand->name : translate('Not Found') }}">
                                            <span
                                                class="badge badge-md badge-dot badge-circle {{ $badge }} mr-2"></span>
                                            {{ $brand ? $brand->name : translate('Not Found') }}
                                        </h3>
                                        <h3 class="fs-13 fw-600 mb-0">
                                            {{ single_price($top_brand->total) }}
                                        </h3>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Net Sales -->
        <div class="col-lg-6">
            <div class="dashboard-box bg-white mb-2rem overflow-hidden p-2rem" style="">
                <!-- Header -->
                <div class="d-sm-flex align-items-center justify-content-between mb-3">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="fs-16 fw-600 text-soft-dark mb-2">{{ translate('Net Sales') }}</h2>
                        <h4 class="fs-13 fw-600 text-secondary mb-0">{{ translate('By Sale Category') }}</h4>
                    </div>
                    <!-- nav -->
                    <ul class="nav nav-tabs dashboard-tab dashboard-tab-danger border-0" role="tablist"
                        aria-orientation="vertical">
                        <li class="nav-item">
                            <a class="nav-link net_sales_tab active" id="all-tab" href="#all" data-toggle="tab"
                                data-target="all" type="button" role="tab" aria-controls="all"
                                aria-selected="true">
                                {{ translate('All') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link net_sales_tab" id="today-tab" href="#today" data-toggle="tab"
                                data-target="DAY" type="button" role="tab" aria-controls="today"
                                aria-selected="true">
                                {{ translate('Today') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link net_sales_tab" id="week-tab" href="#week" data-toggle="tab"
                                data-target="WEEK" type="button" role="tab" aria-controls="week"
                                aria-selected="true">
                                {{ translate('Week') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link net_sales_tab" id="month-tab" href="#month" data-toggle="tab"
                                data-target="MONTH" type="button" role="tab" aria-controls="month"
                                aria-selected="true">
                                {{ translate('Month') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="net_sales" class="mt-3">
                    <canvas id="graph-3" class="w-100" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Payouts -->
        <div class="col-lg-6">
            <div class="dashboard-box bg-white mb-2rem overflow-hidden p-2rem" style="">
                <!-- Header -->
                <div class="d-sm-flex align-items-center justify-content-between mb-3">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="fs-16 fw-600 text-soft-dark mb-2">{{ translate('Payouts') }}</h2>
                        <h4 class="fs-13 fw-600 text-secondary mb-0">{{ translate('By Expense Category') }}</h4>
                    </div>
                    <!-- nav -->
                    <ul class="nav nav-tabs dashboard-tab dashboard-tab-danger border-0" role="tablist"
                        aria-orientation="vertical">
                        <li class="nav-item">
                            <a class="nav-link payouts_tab active" id="all-tab" href="#all"
                                data-toggle="tab" data-target="all" type="button" role="tab" aria-controls="all"
                                aria-selected="true">
                                {{ translate('All') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link payouts_tab" id="today-tab" href="#today"
                                data-toggle="tab" data-target="DAY" type="button" role="tab" aria-controls="today"
                                aria-selected="true">
                                {{ translate('Today') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link payouts_tab" id="week-tab" href="#week"
                                data-toggle="tab" data-target="WEEK" type="button" role="tab" aria-controls="week"
                                aria-selected="true">
                                {{ translate('Week') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link payouts_tab" id="month-tab" href="#month"
                                data-toggle="tab" data-target="MONTH" type="button" role="tab"
                                aria-controls="month" aria-selected="true">
                                {{ translate('Month') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="payouts" class="mt-3">
                    <canvas id="graph-4" class="w-100" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Sale Analytics -->
        <div class="col-lg-6">
            <div class="dashboard-box bg-white mb-2rem overflow-hidden p-2rem" style="">
                <!-- Header -->
                <div class="d-sm-flex align-items-center justify-content-between mb-3">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="fs-16 fw-600 text-soft-dark mb-2">{{ translate('Sale Analytics') }}</h2>
                    </div>
                    <!-- nav -->
                    <ul class="nav nav-tabs dashboard-tab dashboard-tab-danger border-0" role="tablist"
                        aria-orientation="vertical">
                        <li class="nav-item">
                            <a class="nav-link sales_analytics_tab active" id="all-tab" href="#all"
                                data-toggle="tab" data-target="all" type="button" role="tab" aria-controls="all"
                                aria-selected="true">
                                {{ translate('All') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sales_analytics_tab" id="today-tab" href="#today"
                                data-toggle="tab" data-target="TODAY" type="button" role="tab" aria-controls="TODAY"
                                aria-selected="true">
                                {{ translate('Today') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sales_analytics_tab" id="week-tab" href="#week"
                                data-toggle="tab" data-target="WEEK" type="button" role="tab" aria-controls="week"
                                aria-selected="true">
                                {{ translate('Week') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link sales_analytics_tab" id="month-tab" href="#month"
                                data-toggle="tab" data-target="MONTH" type="button" role="tab"
                                aria-controls="month" aria-selected="true">
                                {{ translate('Month') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="sale_analytics" class="mt-3">
                    <canvas id="graph-5" class="w-100" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Payouts Analytics -->
        <div class="col-lg-6">
            <div class="dashboard-box bg-white mb-2rem overflow-hidden p-2rem" style="">
                <!-- Header -->
                <div class="d-sm-flex align-items-center justify-content-between mb-3">
                    <div class="mb-3 mb-sm-0">
                        <h2 class="fs-16 fw-600 text-soft-dark mb-2">{{ translate('Payouts Analytics') }}</h2>
                    </div>
                    <!-- nav -->
                    <ul class="nav nav-tabs dashboard-tab dashboard-tab-danger border-0" role="tablist"
                        aria-orientation="vertical">
                        <li class="nav-item">
                            <a class="nav-link payouts_analytic_tab active" id="all-tab" href="#all"
                                data-toggle="tab" data-target="all" type="button" role="tab" aria-controls="all"
                                aria-selected="true">
                                {{ translate('All') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link payouts_analytic_tab" id="today-tab" href="#today"
                                data-toggle="tab" data-target="TODAY" type="button" role="tab" aria-controls="TODAY"
                                aria-selected="true">
                                {{ translate('Today') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link payouts_analytic_tab" id="week-tab" href="#week"
                                data-toggle="tab" data-target="WEEK" type="button" role="tab" aria-controls="week"
                                aria-selected="true">
                                {{ translate('Week') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link payouts_analytic_tab" id="month-tab" href="#month"
                                data-toggle="tab" data-target="MONTH" type="button" role="tab"
                                aria-controls="month" aria-selected="true">
                                {{ translate('Month') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div id="payouts_analytics" class="mt-3">
                    <canvas id="graph-6" class="w-100" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        net_sales($(".net_sales_tab").data('target'));
        payouts($(".payouts_tab").data('target'));
        sales_analytics($(".sales_analytics_tab").data('target'));
        payouts_analytics($(".payouts_analytic_tab").data('target'));
        // Total Sale Report
        AIZ.plugins.chart('#graph-1', {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($sales_stat as $row)
                        "{{ $row['time'] }} {{ $row['formatted_price'] }}",
                    @endforeach
                ],
                datasets: [{
                    fill: false,
                    borderColor: '#1D82FA',
                    backgroundColor: '#1D82FA',
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 8,
                    data: [
                        @foreach ($sales_stat as $row)
                            {{ $row['total'] }},
                        @endforeach
                    ],
                }, ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false
                    }
                }
            },
        });

        // Seller Payout Report
        AIZ.plugins.chart('#graph-2', {
            type: 'bar',
            data: {
                labels: [
                    @foreach ($payout_stat as $row)
                        "{{ $row['time'] }} {{ $row['formatted_price'] }}",
                    @endforeach
                ],
                datasets: [{
                    fill: false,
                    borderColor: '#F0416C',
                    backgroundColor: '#F0416C',
                    borderWidth: 2,
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 8,
                    data: [
                        @foreach ($payout_stat as $row)
                            {{ $row['total'] }},
                        @endforeach
                    ],
                }, ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        display: false
                    },
                    y: {
                        display: false
                    }
                }
            },
        });

        var net_sales_graph = document.getElementById('graph-3');
        var payouts_graph = document.getElementById('graph-4');
        var sale_analytic_graph = document.getElementById('graph-5');
        var payouts_analytic_graph = document.getElementById('graph-6');
        
        var net_sales_chart;
        var payouts_chart;
        var sale_analytic_chart;
        var payouts_analytic_chart;

        // Net Sales
        net_sales_chart = new Chart(net_sales_graph, {
            type: 'bar',
            data: {
                labels: [
                    "{{ translate('Product Sales') }}",
                    "{{ translate('Commission') }}",
                    "{{ translate('Seller Subscription') }}",
                    "{{ translate('Customer Subscription') }}",
                    "{{ translate('Delivery') }}"
                ],
                datasets: [{
                    axis: 'y',
                    data: [],
                    fill: false,
                    borderWidth: 1,
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30,
                    backgroundColor: [
                        '#1D82FA',
                        '#FE884D',
                        '#8F60EE',
                        '#51CC8A',
                        '#FFC700',
                    ],
                    borderColor: [
                        '#1D82FA',
                        '#FE884D',
                        '#8F60EE',
                        '#51CC8A',
                        '#FFC700',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        display: true,
                        border: {
                            dash: [5, 5]
                        },
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return number_format_short(value);
                            }
                        }
                    },
                    y: {
                        display: true,
                        border: {
                            display: false,
                        },
                        grid: {
                            display: false,
                        },
                    }
                }
            }
        });

        $(".net_sales_tab").on("click", function() {
            net_sales($(this).data('target'))
        });

        function net_sales(interval_type) {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: "POST",
                url: AIZ.data.appUrl +
                    "/admin/reports/earning-payout-report/net-sales",
                data: {
                    interval_type: interval_type,
                },
                success: function(data) {
                    net_sales_chart.data.datasets[0].data[0] = data.product_sale;
                    net_sales_chart.data.datasets[0].data[1] = data.commission;
                    net_sales_chart.data.datasets[0].data[2] = data.seller_subscription;
                    net_sales_chart.data.datasets[0].data[3] = data.customer_subscription;
                    net_sales_chart.data.datasets[0].data[4] = data.delivery;
    
                    net_sales_chart.update();
                },
            });
        }

        // Payouts
        payouts_chart = new Chart(payouts_graph, {
            type: 'bar',
            data: {
                labels: ["{{ translate('Seller Payout') }}", "{{ translate('Product Refund') }}", "{{ translate('Delivery Boy') }}"],
                datasets: [{
                    axis: 'y',
                    data: [],
                    backgroundColor: [
                        '#51CC8A',
                        '#f0416c',
                        '#FFC700'
                    ],
                    borderColor: [
                        '#51CC8A',
                        '#f0416c',
                        '#FFC700'
                    ],
                    borderWidth: 1,
                    borderSkipped: false,
                    borderRadius: 5,
                    barThickness: 30,
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        border: {
                            dash: [5, 5]
                        },
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return number_format_short(value);
                            }
                        },
                    },
                    y: {
                        border: {
                            display: false,
                        },
                        grid: {
                            display: false
                        }
                    }

                }
            },
        
        });

        $(".payouts_tab").on("click", function() {
            payouts($(this).data('target'));
        });

        function payouts(interval_type) {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: "POST",
                url: AIZ.data.appUrl +
                    "/admin/reports/earning-payout-report/payouts",
                data: {
                    interval_type: interval_type,
                },
                success: function(data) {
                    payouts_chart.data.datasets[0].data[0] = data.seller_payout;
                    payouts_chart.data.datasets[0].data[1] = data.product_refund;
                    payouts_chart.data.datasets[0].data[2] = data.delivery_boy_payout;
    
                    payouts_chart.update();
                },
            });
        }

        
        // Sale Analytics chart
        sale_analytic_chart = new Chart(sale_analytic_graph, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    fill: false,
                    borderColor: [],
                    backgroundColor: [],
                    borderWidth: 2,
                    borderRadius: 2,
                    borderSkipped: false,
                    barThickness: 15,
                    data: [],
                }, ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: false
                        },
                    },
                    y: {
                        display: true,
                        border: {
                            dash: [5, 5]
                        },
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return number_format_short(value);
                            }
                        }
                    }
                }
            },
        });

        $(".sales_analytics_tab").on("click", function() {
            sales_analytics($(this).data('target'))
        })

        function sales_analytics(interval_type) {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: "POST",
                url: AIZ.data.appUrl +
                    "/admin/reports/earning-payout-report/sale-analytic",
                data: {
                    interval_type: interval_type,
                },
                success: function(data) {
                    sale_analytic_chart.data.labels = [];
                    sale_analytic_chart.data.datasets[0].data = [];
                    sale_analytic_chart.data.datasets[0].borderColor = [];
                    sale_analytic_chart.data.datasets[0].backgroundColor = [];
                    data.forEach((sale, index, array) => {
                        sale_analytic_chart.data.labels.push(sale.time);
                        sale_analytic_chart.data.datasets[0].data.push(sale.total);
                        sale_analytic_chart.data.datasets[0].borderColor.push(sale.bg_color);
                        sale_analytic_chart.data.datasets[0].backgroundColor.push(sale.bg_color);
                    })
                    
                    sale_analytic_chart.update();
                },
            });
        }

        // Payouts Analytics chart
        payouts_analytic_chart = new Chart(payouts_analytic_graph, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    fill: false,
                    borderColor: [],
                    backgroundColor: [],
                    borderWidth: 2,
                    borderRadius: 2,
                    borderSkipped: false,
                    barThickness: 15,
                    data: [],
                }, ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: false
                        },
                    },
                    y: {
                        display: true,
                        border: {
                            dash: [5, 5]
                        },
                        ticks: {
                            beginAtZero: true,
                            callback: function(value, index, values) {
                                return number_format_short(value);
                            }
                        }
                    }
                }
            },
        });

        $(".payouts_analytic_tab").on("click", function() {
            payouts_analytics($(this).data('target'))
        })

        function payouts_analytics(interval_type) {
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                type: "POST",
                url: AIZ.data.appUrl +
                    "/admin/reports/earning-payout-report/payout-analytic",
                data: {
                    interval_type: interval_type,
                },
                success: function(data) {
                    payouts_analytic_chart.data.labels = [];
                    payouts_analytic_chart.data.datasets[0].data = [];
                    payouts_analytic_chart.data.datasets[0].borderColor = [];
                    payouts_analytic_chart.data.datasets[0].backgroundColor = [];
                    data.forEach((sale, index, array) => {
                        payouts_analytic_chart.data.labels.push(sale.time);
                        payouts_analytic_chart.data.datasets[0].data.push(sale.total);
                        payouts_analytic_chart.data.datasets[0].borderColor.push(sale.bg_color);
                        payouts_analytic_chart.data.datasets[0].backgroundColor.push(sale.bg_color);
                    })
                    
                    payouts_analytic_chart.update();
                },
            });
        }

        function number_format_short(n) {
            if (n < 1e3) return n;
            if (n >= 1e3 && n < 1e6) return +(n / 1e3).toFixed(1) + "K";
            if (n >= 1e6 && n < 1e9) return +(n / 1e6).toFixed(1) + "M";
            if (n >= 1e9 && n < 1e12) return +(n / 1e9).toFixed(1) + "B";
            if (n >= 1e12) return +(n / 1e12).toFixed(1) + "T";
        }
    </script>
@endsection
