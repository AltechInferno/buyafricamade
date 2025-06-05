@extends('backend.layouts.app')

@section('content')

@if (env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
    <div class="alert alert-info d-flex align-items-center">
        {{ translate('You need to configure SMTP correctly to to add Customer by email.') }}
        <a class="alert-link ml-2" href="{{ route('smtp_settings.index') }}">{{ translate('Configure Now') }}</a>
    </div>
@endif

<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Customer Information')}}</h5>
        </div>
        
        <form action="{{ route('customers.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="name">
                        {{translate('Name')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control @if ($errors->has('name')) is-invalid @endif" name="name" value="{{ old('name') }}" placeholder="{{ translate('Name') }}" required>
                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                @if (addon_is_activated('otp_system'))
                    <div class="form-group row phone-form-group mb-1">
                        <label for="phone" class="col-sm-2 col-from-label">
                            {{ translate('Phone') }} <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="tel" id="phone-code" class="form-control rounded-0 @if($errors->has('phone')) is-invalid @endif" value="{{ old('email') }}" value="{{ old('phone') }}" placeholder="" name="phone" autocomplete="off">
                        </div>
                    </div>

                    <input type="hidden" name="country_code" value="">

                    <div class="form-group row email-form-group mb-1 d-none">
                        <label for="email" class="col-sm-2 col-from-label">
                            {{  translate('Email') }} <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control rounded-0 @if($errors->has('email')) is-invalid @endif" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email"  autocomplete="off">
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group text-right">
                        <button class="btn btn-link p-0 text-primary" type="button" onclick="toggleEmailPhone(this)"><i>*{{ translate('Use Email Instead') }}</i></button>
                    </div>
                @else
                    <div class="form-group row">
                        <label for="email" class="col-sm-2 col-from-label">
                            {{ translate('Email') }} <span class="text-danger">*</span>
                        </label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control rounded-0 @if($errors->has('email')) is-invalid @endif" value="{{ old('email') }}" placeholder="{{  translate('Email') }}" name="email">
                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
                {{-- <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="email">
                        {{translate('Password')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control @if($errors->has('password')) is-invalid @endif" name="password" value="{{ old('password') }}" placeholder="{{translate('Password')}}" required>
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-from-label" for="email">
                        {{translate('Confirm Password')}} <span class="text-danger">*</span>
                    </label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" name="password_confirmation" value="{{ old('password_confirmation') }}" placeholder="{{translate('Confirm Password')}}" required>
                    </div>
                </div> --}}
                
                
                <div class="form-group mb-3 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        // Country Code
        var isPhoneShown = true,
            countryData = window.intlTelInputGlobals.getCountryData(),
            input = document.querySelector("#phone-code");

        for (var i = 0; i < countryData.length; i++) {
            var country = countryData[i];
            if (country.iso2 == 'bd') {
                country.dialCode = '88';
            }
        }

        var iti = intlTelInput(input, {
            separateDialCode: true,
            utilsScript: "{{ static_asset('assets/js/intlTelutils.js') }}?1590403638580",
            onlyCountries: @php echo get_active_countries()->pluck('code') @endphp,
            customPlaceholder: function(selectedCountryPlaceholder, selectedCountryData) {
                if (selectedCountryData.iso2 == 'bd') {
                    return "01xxxxxxxxx";
                }
                return selectedCountryPlaceholder;
            }
        });

        var country = iti.getSelectedCountryData();
        $('input[name=country_code]').val(country.dialCode);

        input.addEventListener("countrychange", function(e) {
            // var currentMask = e.currentTarget.placeholder;
            var country = iti.getSelectedCountryData();
            $('input[name=country_code]').val(country.dialCode);

        });

        function toggleEmailPhone(el) {
            if (isPhoneShown) {
                $('.phone-form-group').addClass('d-none');
                $('.email-form-group').removeClass('d-none');
                $('input[name=phone]').val(null);
                isPhoneShown = false;
                $(el).html('*{{ translate('Use Phone Number Instead') }}');
            } else {
                $('.phone-form-group').removeClass('d-none');
                $('.email-form-group').addClass('d-none');
                $('input[name=email]').val(null);
                isPhoneShown = true;
                $(el).html('<i>*{{ translate('Use Email Instead') }}</i>');
            }
        }
    </script> 
@endsection
