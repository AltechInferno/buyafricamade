@extends('frontend.layouts.app')

@section('meta_title'){{ $page->meta_title }}@stop

@section('meta_description'){{ $page->meta_description }}@stop

@section('meta_keywords'){{ $page->tags }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $page->meta_title }}">
    <meta itemprop="description" content="{{ $page->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($page->meta_image) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="website">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $page->meta_title }}">
    <meta name="twitter:description" content="{{ $page->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($page->meta_image) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $page->meta_title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ URL($page->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($page->meta_image) }}" />
    <meta property="og:description" content="{{ $page->meta_description }}" />
    <meta property="og:site_name" content="{{ env('APP_NAME') }}" />
@endsection

@section('content')
<section class="pt-4 my-4">
    @php
        $lang = str_replace('_', '-', app()->getLocale());
        $content = json_decode($page->getTranslation('content', $lang));
    @endphp
    <div class="container">
        <div class="" style="background-color: {{ hex2rgba(get_setting('base_color', '#d43533'), 0.02) }}">
            <div class="row">
                <div class="col-lg-6 text-center text-lg-left">
                    <div class="p-3 p-md-4 p-xl-5">
                        <h1 class="fs-36 fw-700 mb-4">{{ $page->getTranslation('title') }}</h1>
                        <p class="fs-16 fw-400 mb-5">{{ $content->description }}</p>
                        <div class="d-flex mb-5">
                            <span class="size-48px d-flex align-items-center justify-content-center border border-gray-500 rounded-content">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19.201" height="24" viewBox="0 0 19.201 24">
                                    <path id="c2b0eedccc4761c59dc63e9987216605" d="M13.6,2A9.611,9.611,0,0,0,4,11.6c0,3.906,2.836,7.15,5.839,10.583.95,1.087,1.934,2.212,2.81,3.349a1.2,1.2,0,0,0,1.9,0c.876-1.138,1.86-2.262,2.81-3.349,3-3.433,5.839-6.677,5.839-10.583A9.611,9.611,0,0,0,13.6,2Zm0,13.2a3.6,3.6,0,1,1,3.6-3.6A3.6,3.6,0,0,1,13.6,15.2Z" transform="translate(-4 -2)" fill="#9d9da6"/>
                                </svg>
                            </span>
                            <span class="ml-3">
                                <span class="fs-19 fw-700">{{ translate('Address') }}</span><br>
                                <span class="fs-14 text-secondary">{!! str_replace("\n", "<br>", $content->address) !!}</span>
                            </span>
                        </div>
                        <div class="d-flex mb-5">
                            <span class="size-48px d-flex align-items-center justify-content-center border border-gray-500 rounded-content">
                                <i class="las la-2x la-phone text-gray"></i>
                            </span>
                            <span class="ml-3">
                                <span class="fs-19 fw-700">{{ translate('Phone') }}</span><br>
                                <span class="fs-14 text-secondary">{{ $content->phone }}</span>
                            </span>
                        </div>
                        <div class="d-flex">
                            <span class="size-48px d-flex align-items-center justify-content-center border border-gray-500 rounded-content">
                                <i class="las la-2x la-envelope text-gray"></i>
                            </span>
                            <span class="ml-3">
                                <span class="fs-19 fw-700">{{ translate('Email Address') }}</span><br>
                                <span class="fs-14 text-secondary">{{ $content->email }}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="p-3 p-md-4 p-xl-5">
                        <div class="bg-white p-4 p-xl-2rem border rounded-3">
                            <form class="form-default" role="form" action="{{ route('contact') }}" method="POST">
                                @csrf

                                <!-- Name -->
                                <div class="form-group">
                                    <label for="name" class="fs-14 fw-700 text-soft-dark">{{  translate('Name') }}</label>
                                    <input type="text" class="form-control rounded-0" value="{{ old('name') }}" placeholder="{{  translate('Enter Name') }}" name="name" required>
                                </div>
                                <!-- Email -->
                                <div class="form-group">
                                    <label for="email" class="fs-14 fw-700 text-soft-dark">{{  translate('Email') }}</label>
                                    <input type="email" class="form-control rounded-0" value="{{ old('email') }}" placeholder="{{  translate('Enter Email') }}" name="email" required>
                                </div>
                                <!-- Phone -->
                                <div class="form-group">
                                    <label for="phone" class="fs-14 fw-700 text-soft-dark">{{  translate('Phone no. (optional)') }}</label>
                                    <input type="tel" class="form-control rounded-0" value="{{ old('phone') }}" placeholder="{{  translate('Enter Phone') }}" name="phone">
                                </div>
                                <!-- Query -->
                                <div class="form-group">
                                    <label for="query" class="fs-14 fw-700 text-soft-dark">{{  translate('Tell us about your query') }}</label>
                                    <textarea
                                        class="form-control rounded-0"
                                        placeholder="{{translate('Type here...')}}"
                                        name="content"
                                        rows="3"
                                        required
                                    ></textarea>
                                </div>

                                <!-- Recaptcha -->
                                @if(get_setting('google_recaptcha') == 1)
                                    <div class="form-group">
                                        <div class="g-recaptcha" data-sitekey="{{ env('CAPTCHA_KEY') }}"></div>
                                    </div>
                                    @if ($errors->has('g-recaptcha-response'))
                                        <span class="invalid-feedback" role="alert" style="display: block;">
                                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                        </span>
                                    @endif
                                @endif

                                <!-- Submit Button -->
                                <div class="mt-4">
                                    @if (env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
                                        <a class="btn btn-primary fw-700 fs-14 rounded-0 w-200px"
                                            href="javascript:void(1)" onclick="showWarning()">
                                            {{  translate('Submit') }}
                                        </a>
                                    @else
                                        <button type="submit" class="btn btn-primary fw-700 fs-14 rounded-0 w-200px">{{  translate('Submit') }}</button>
                                    @endif

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
    @if(get_setting('google_recaptcha') == 1)
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    @endif
    
    <script type="text/javascript">
        @if(get_setting('google_recaptcha') == 1)
        // making the CAPTCHA  a required field for form submission
        $(document).ready(function(){
            $("#reg-form").on("submit", function(evt)
            {
                var response = grecaptcha.getResponse();
                if(response.length == 0)
                {
                //reCaptcha not verified
                    alert("please verify you are human!");
                    evt.preventDefault();
                    return false;
                }
                //captcha verified
                //do the rest of your validations here
                $("#reg-form").submit();
            });
        });
        @endif
    </script>


    <script type="text/javascript">
        function showWarning(){
            AIZ.plugins.notify('warning', "{{ translate('Something went wrong.') }}");
            return false;
        }
    </script>
@endsection
