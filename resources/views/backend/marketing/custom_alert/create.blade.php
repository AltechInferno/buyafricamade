@extends('backend.layouts.app')

@section('content')
    <style>
        .alert-type{
            border: 2px solid transparent;    
            border-radius: 6px;
            padding: 8px;
            transition: 0.4s;
        }
        .alert-type.active,
        .alert-type:hover{
            border-color: var(--primary);
        }
    </style>
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Add New Custom Alerts') }}</h5>
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
        
        <form class="form form-horizontal mar-top" action="{{ route('custom-alerts.store') }}" method="POST" enctype="multipart/form-data" id="submitForm">
            @csrf
            <!-- Custom Alert Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Custom Alert Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row gutters-16">
                        <div class="col-lg-8">
                            <!-- Select Alert Size -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Select Alert Size')}} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8 row">
                                    <div class="col-6">
                                        <label class="aiz-megabox d-block bg-white mb-0">
                                            <input
                                                type="radio"
                                                name="type"
                                                value="small"
                                                onchange="show_image_type(this)"
                                                data-target="custom-alert-small"
                                                checked
                                            >
                                            <span class="d-flex align-items-center aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">
                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-3 fw-600">{{  translate('Small') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="aiz-megabox d-block bg-white mb-0">
                                            <input
                                                type="radio"
                                                name="type"
                                                value="large"
                                                onchange="show_image_type(this)"
                                                data-target="custom-alert-large"
                                            >
                                            <span class="d-flex align-items-center aiz-megabox-elem rounded-0" style="padding: 0.75rem 1.2rem;">
                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-3 fw-600">{{  translate('Large') }}</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <!-- Image -->
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label fw-700" for="banner">
                                    {{ translate('Image') }} <span class="text-danger">*</span><br>
                                    <span class="fs-12 text-secondary fw-400 img_size_guide" id="img_size_guide_small">{{ translate('(120px X 140px)') }}</span>
                                    <span class="fs-12 text-secondary fw-400 img_size_guide d-none" id="img_size_guide_large">{{ translate('(300px X 160px)') }}</span>
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
                            <!-- Link -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Link')}} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="link" placeholder="{{ translate('Type your text here') }}" required>
                                </div>
                            </div>
                            <!-- Text -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Text')}} <span class="text-danger">*</span><br>
                                    <span class="fs-12 text-secondary fw-400">{{ translate('(Best within 200 character)') }}</span>
                                </label>
                                <div class="col-md-8">
                                    <textarea class="form-control" name="description" rows="2" placeholder="{{ translate('Type your text here') }}" required></textarea>
                                </div>
                            </div>
                            <!-- Select Background Color -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Select Background Color')}} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control aiz-color-input" placeholder="#000000" name="background_color" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text p-0">
                                                <input class="aiz-color-picker border-0 size-40px" type="color">
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Select Text Color -->
                            <div class="form-group row">
                                <label class="col-md-4 col-from-label fw-700">
                                    {{translate('Select Text Color')}} <span class="text-danger">*</span>
                                </label>
                                <div class="col-md-8 row">
                                    <div class="col radio mar-btm mr-3 d-flex align-items-center">
                                        <input id="text_color_light" class="magic-radio" type="radio" name="text_color" value="white" checked>
                                        <label for="text_color_light" class="mb-0 ml-2">{{translate('Light')}}</label>
                                    </div>
                                    <div class="col radio mar-btm mr-3 d-flex align-items-center">
                                        <input id="text_color_dark" class="magic-radio" type="radio" name="text_color" value="dark">
                                        <label for="text_color_dark" class="mb-0 ml-2">{{translate('Dark')}}</label>
                                    </div>
                                </div>
                            </div>
                            <!-- Button -->
                            <div class="float-right mb-3">
                                <button type="submit" class="btn btn-primary w-230px btn-md rounded-2 fs-14 fw-700 shadow-primary">{{ translate('Save') }}</button>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="h-100 rounded-3 overflow-hideen">
                                <img class="w-100 custom-alert custom-alert-small" src="{{ static_asset('assets/img/custom-alert-small.png') }}" alt="custom alert small">
                                <img class="w-100 custom-alert custom-alert-large d-none" src="{{ static_asset('assets/img/custom-alert-large.png') }}" alt="custom alert large">
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
        function show_image_type(el) {
        	var target = $(el).data('target');
            $('.custom-alert').addClass('d-none');
            $('.'+target).removeClass('d-none');
            var size = $(el).val();
            $('.img_size_guide').addClass('d-none');
            $('#img_size_guide_'+size).removeClass('d-none');
        }
        $('#submitForm').submit(function() {
            $(this).find("button[type='submit']").prop('disabled',true);
        });
    </script>
@endsection
