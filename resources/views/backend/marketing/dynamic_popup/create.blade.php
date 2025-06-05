@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Add New Dynamic Popup') }}</h5>
    </div>
    <div class="">
        <!-- Error Meassages -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form class="form form-horizontal mar-top" action="{{ route('dynamic-popups.store') }}" method="POST" enctype="multipart/form-data" id="submitForm">
            @csrf
            <!-- Custom Dynamic Popup Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Custom Dynamic Popup Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row gutters-16">
                        <div class="col-lg-8">
                            <!-- Title -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Title')}} <span class="text-danger">*</span><br>
                                    <span class="fs-12 text-secondary fw-400">{{ translate('(Best within 50 character)') }}</span>
                                </label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="title" placeholder="{{ translate('Type your text here') }}" required>
                                </div>
                            </div>
                            <!-- Summary -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Summary')}} <span class="text-danger">*</span><br>
                                    <span class="fs-12 text-secondary fw-400">{{ translate('(Best within 200 character)') }}</span>
                                </label>
                                <div class="col-md-8">
                                    <textarea class="form-control" name="summary" rows="2" placeholder="{{ translate('Type your text here') }}" required></textarea>
                                </div>
                            </div>
                            <!-- Image -->
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label fw-700" for="banner">
                                    {{ translate('Image') }} <span class="text-danger">*</span><br>
                                    <span class="fs-12 text-secondary fw-400">{{ translate('(512px X 280px)') }}</span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="banner" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            <!-- Button Text -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Button Text')}} <span class="text-danger">*</span><br>
                                    <span class="fs-12 text-secondary fw-400">{{ translate('(Best within 30 character)') }}</span>
                                </label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="btn_text" placeholder="{{ translate('Type your text here') }}" required>
                                </div>
                            </div>
                            <!-- Select Button Color -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Select Button Color')}} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control aiz-color-input" placeholder="#000000" name="btn_background_color" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text p-0">
                                                <input class="aiz-color-picker border-0 size-40px" type="color">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Button Text Color -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Button Text Color')}} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8 row">
                                    <div class="col radio mar-btm mr-3 d-flex align-items-center">
                                        <input id="btn_text_color_light" class="magic-radio" type="radio" name="btn_text_color" value="white" checked>
                                        <label for="btn_text_color_light" class="mb-0 ml-2">{{translate('Light')}}</label>
                                    </div>
                                    <div class="col radio mar-btm mr-3 d-flex align-items-center">
                                        <input id="btn_text_color_dark" class="magic-radio" type="radio" name="btn_text_color" value="dark">
                                        <label for="btn_text_color_dark" class="mb-0 ml-2">{{translate('Dark')}}</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Link -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Link')}} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="btn_link" placeholder="{{ translate('Type your text here') }}" required>
                                </div>
                            </div>
                            <!-- Button -->
                            <div class="float-right mb-3">
                                <button type="submit" class="btn btn-primary w-230px btn-md rounded-2 fs-14 fw-700 shadow-primary" onclick="var e=this;setTimeout(function(){e.disabled=true;},0);return true;">{{ translate('Save') }}</button>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="h-100 rounded-3 pverflow-hideen">
                                <img class="h-100 w-100" src="{{ static_asset('assets/img/dynamic-popup.png') }}" alt="Dynamic Popup">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#submitForm').submit(function() {
            $(this).find("button[type='submit']").prop('disabled',true);
        });
    </script>
@endsection
