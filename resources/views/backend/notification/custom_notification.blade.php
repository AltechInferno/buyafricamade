@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-body" style="min-height:460px;">
                <form class="form-horizontal" action="{{ route('custom_notification.send') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <p class="fs-13 fw-700 mb-3">{{ translate('Send Custom Notification') }}</p>

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

                    <div class="form-group row">
                        <label class="col-sm-3 control-label fw-700" for="name">{{ translate('Customers') }}</label>
                        <div class="col-sm-9">
                            <select class="form-control form-control-sm aiz-selectpicker" data-live-search="true"
                                name="user_ids[]" id="customers" multiple required>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} -
                                        ({{ $customer->email ?? $customer->phone }})</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-sm btn-soft-blue fw-600 mt-2 rounded-2"
                                id="select_all">{{ translate('Select All') }}</button>
                            <button type="button" class="btn btn-sm btn-soft-blue fw-600 mt-2 rounded-2"
                                id="deselect_all" class="mb-3">{{ translate('Deselect All') }}</button>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label fw-700"
                            for="name">{{ translate('Select Type') }}</label>
                        <div class="col-sm-9">
                            <select class="form-control form-control-sm aiz-selectpicker" data-live-search="true"
                                onchange="getContent(this.value)" name="notification_type_id" required>
                                <option value="">{{ translate('Select the type of the notification') }}</option>
                                @foreach ($customNotificationTypes as $customNotificationType)
                                    <option value="{{ $customNotificationType->id }}">
                                        {{ $customNotificationType->getTranslation('name') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 control-label fw-700" for="name">
                            {{ translate('Content') }}
                            <br>
                            <span
                                class="fs-12 text-secondary fw-400">({{ translate('Best within 80 character') }})</span>
                        </label>
                        <div class="col-sm-9">
                            <textarea class="form-control form-control-sm" id="notification_content" rows="4"
                                placeholder="{{ translate('Write what your notification will display…') }}" readonly></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 control-label fw-700" for="link">{{ translate('Link') }}</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control form-control-sm" name="link"
                                placeholder="{{ translate('Paste your link here') }}">
                        </div>
                    </div>
                    <div class="float-right my-3">
                        <button type="submit"
                            class="btn btn-primary btn-sm fw-700 rounded-2 shadow-primary w-170px">{{ translate('Send Notifications') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
    <script type="text/javascript">
        // Customer Select and deselect to send custom notification
        $('#select_all').click(function() {
            $('.aiz-selectpicker').selectpicker('selectAll');
        });

        $("#deselect_all").click(function() {
            $('.aiz-selectpicker').selectpicker('deselectAll');
        });

        // Get default content by the notification type
        function getContent(id) {
            $('#notification_content').prop("disabled", true);
            $.post('{{ route('notification_type.get_default_text') }}', {
                _token: '{{ @csrf_token() }}',
                id: id,
            }, function(data) {
                if (data != null) {
                    $('textarea#notification_content').val(data);
                }
            });
        }
    </script>
@endsection
