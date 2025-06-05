@extends('backend.layouts.app')

@section('content')
<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-body" style="background-color: #fcfcfc; min-height:460px;">
            <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @php
                    $notification_show_type = get_setting('notification_show_type');
                @endphp
                <input type="hidden" name="types[]" value="notification_show_type">
                <h1 class="fs-13 fw-700 mb-3">{{translate('Notification Settings')}}</h1>
                <p>{{ translate('You can add new types & upload image for every type. If you do not upload image or edit images from default types then default image will be shown.') }}</p>
                <div class="row mt-md-5 gutters-20">
                    <div class="col-md-6 mb-4">
                        <div class="shadow-lg bg-white rounded">
                            <p class="px-4 py-2">{{ translate('Order code') }} : <span class="fw-600 text-blue">20220912-10085522</span> {{ translate('has been Delivered') }}</p>
                        </div>
                        <div class="align-items-center d-flex">
                            <input id="only_text" class="magic-radio" type="radio" name="notification_show_type" value="only_text" @if($notification_show_type == 'only_text') checked @endif>
                            <label for="only_text" class="mb-0 ml-2">{{translate('Only Text ')}}</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="shadow-lg bg-white rounded d-flex align-items-center px-4 py-2">
                            <img src="{{ static_asset('assets/img/notification.png') }}" height="35">
                            <span class="pl-2">{{ translate('Order code') }} : <span class="fw-600 text-blue">20220912-10085522</span> {{ translate('has been Delivered') }}</span>
                        </div>
                        <div class="align-items-center d-flex mt-3">
                            <input id="design_1" class="magic-radio" type="radio" name="notification_show_type" value="design_1" @if($notification_show_type == 'design_1') checked @endif>
                            <label for="design_1" class="mb-0 ml-2">{{translate('Design 1')}}</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="shadow-lg bg-white rounded d-flex align-items-center px-4 py-2">
                            <img src="{{ static_asset('assets/img/notification.png') }}" height="35" class="rounded-1">
                            <span class="pl-2">{{ translate('Order code') }} : <span class="fw-600 text-blue">20220912-10085522</span> {{ translate('has been Delivered') }}</span>
                        </div>
                        <div class="align-items-center d-flex mt-3">
                            <input id="design_2" class="magic-radio" type="radio" name="notification_show_type" value="design_2" @if($notification_show_type == 'design_2') checked @endif>
                            <label for="design_2" class="mb-0 ml-2">{{translate('Design 2')}}</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="shadow-lg bg-white rounded d-flex align-items-center px-4 py-2">
                            <img src="{{ static_asset('assets/img/notification.png') }}" height="35" class="rounded-circle">
                            <span class="pl-2">{{ translate('Order code') }} : <span class="fw-600 text-blue">20220912-10085522</span> {{ translate('has been Delivered') }}</span>
                        </div>
                        <div class="align-items-center d-flex mt-3">
                            <input id="design_3" class="magic-radio" type="radio" name="notification_show_type" value="design_3" @if($notification_show_type == 'design_3') checked @endif>
                            <label for="design_3" class="mb-0 ml-2">{{translate('Design 3')}}</label>
                        </div>
                    </div>
                </div>
                <div class="float-right my-3">
                    <button type="submit" class="btn btn-primary btn-sm fw-700 rounded-2 shadow-primary w-140px">{{ translate('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
