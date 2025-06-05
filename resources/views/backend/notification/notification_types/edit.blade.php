@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <h5 class="mb-0 h6">{{ translate('Notification Type Information') }}</h5>
    </div>

    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-fill language-bar">
                    @foreach (get_all_active_language() as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3"
                                href="{{ route('notification-type.edit', ['id' => $notificationType->id, 'lang' => $language->code]) }}">
                                <img src="{{ static_asset('assets/img/flags/' . $language->code . '.png') }}" height="11"
                                    class="mr-1">
                                <span>{{ $language->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <form class="p-4" action="{{ route('notification-type.update', $notificationType->id) }}" method="POST"
                    enctype="multipart/form-data">
                    <input name="_method" type="hidden" value="PATCH">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    @csrf

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="name">{{ translate('Name') }} <i
                                class="las la-language text-danger" title="{{ translate('Translatable') }}"></i></label>
                        <div class="col-md-9">
                            <input type="text" placeholder="{{ translate('Notification Type') }}" id="name"
                                name="name" value="{{ $notificationType->getTranslation('name', $lang) }}"
                                class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-form-label" for="signinSrEmail">{{ translate('Image') }}
                            <small>({{ translate('36x36') }})</small></label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">
                                        {{ translate('Browse') }}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                <input type="hidden" name="image" value="{{ $notificationType->image }}"
                                    class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-3 col-from-label" for="default_text">
                            {{ translate('Default Text') }}<i class="las la-language text-danger"
                                title="{{ translate('Translatable') }}"></i>
                            <br>
                            <span class="fs-12 text-secondary fw-400">({{ translate('Best within 80 character') }})</span>
                        </label>
                        <div class="col-md-9">
                            <textarea type="text" name="default_text" id="default_text" rows="4" class="form-control"
                                placeholder="{{ translate('Default Text') }}" required>{{ $notificationType->getTranslation('default_text', $lang) }}</textarea>
                            @if ($notificationType->type != 'custom')
                                <small
                                    class="form-text text-danger">{{ translate('N.B : Do Not Change The Variables Like') }}
                                    [[ ____ ]]</small>
                            @endif
                            <small
                                class="form-text text-danger">{{ translate('N.B : Use character, number only') }}</small>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
