@extends('backend.layouts.app')

@section('content')

    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="align-items-center">
            <h1 class="h3">{{ translate('All Notification Types') }}</h1>
        </div>
    </div>

    <div class="row">
        {{-- Notification Type List --}}
        <div class="@if(auth()->user()->can('add_notification_types')) col-lg-7 @else col-lg-12 @endif">
            <div class="card">
                <div class="d-sm-flex justify-content-between mt-4 mx-4">
                    <div>
                        <p class="fs-13 fw-700 mb-0">{{ translate('All Notification Types') }}</p>
                        <small class="fs-11">{{ translate('Default notification types can not be deleted.') }}</small>
                    </div>
                </div>

                <form class="" id="sort_notification_types" action="" method="GET">
                    <input type="hidden" name="notification_user_type" value="{{ $notificationUserType }}">
                    <div class="d-sm-flex justify-content-between mx-4">
                        <div class="mt-3">
                            @php
                                $activeClasss = 'btn-soft-blue';
                                $inActiveClasses = 'text-secondary border-dashed border-soft-light';
                            @endphp
                            <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $notificationUserType == 'customer' ? $activeClasss : $inActiveClasses }}"
                                href="javascript:void(0);" onclick="sort_notification_types('customer')">
                                {{ translate('Customer') }}
                            </a>
                            <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $notificationUserType == 'seller' ? $activeClasss : $inActiveClasses }}"
                                href="javascript:void(0);" onclick="sort_notification_types('seller')">
                                {{ translate('Seller') }}
                            </a>
                            <a class="btn btn-sm btn-circle fs-12 fw-600 mr-2 {{ $notificationUserType == 'admin' ? $activeClasss : $inActiveClasses }}"
                                href="javascript:void(0);" onclick="sort_notification_types('admin')">
                                {{ translate('Admin') }}
                            </a>
                        </div>
                        <div class="d-flex mt-3">
                            @can('delete_notification_types')
                                <div class="dropdown mb-md-0 mr-2">
                                    <button class="btn btn-sm border dropdown-toggle text-secondary" type="button"
                                        data-toggle="dropdown">
                                        {{ translate('Bulk Action') }}
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item confirm-alert" href="javascript:void(0)"
                                            data-target="#bulk-delete-modal"> {{ translate('Delete selection') }}</a>
                                    </div>
                                </div>
                            @endcan
                            <div class="form-group mb-0">
                                <input type="text" class="form-control form-control-sm h-100"
                                    name="notification_type_sort_search"
                                    @isset($notification_type_sort_search) value="{{ $notification_type_sort_search }}" @endisset
                                    placeholder="{{ translate('Type & Enter') }}">
                            </div>
                        </div>
                    </div>
                </form>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table aiz-table mb-0">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <div class="form-group">
                                            <div class="aiz-checkbox-inline">
                                                <label class="aiz-checkbox">
                                                    <input type="checkbox" class="check-all">
                                                    <span class="aiz-square-check"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </th>
                                    <th>{{ translate('Image') }}</th>
                                    <th width="25%">{{ translate('Type') }}</th>
                                    <th width="40%">{{ translate('DEfault Text') }}</th>
                                    <th>{{ translate('Status') }}</th>
                                    <th class="text-right">{{ translate('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notificationTypes as $key => $notificationType)
                                    <tr>
                                        <td>
                                            @if ($notificationType->type != 'custom')
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="18"
                                                    viewBox="0 0 16 20">
                                                    <path id="df12b5039313fc3798dfa93cfb504acd"
                                                        d="M17,9V7A5,5,0,0,0,7,7V9a2.946,2.946,0,0,0-3,3v7a2.946,2.946,0,0,0,3,3H17a2.946,2.946,0,0,0,3-3V12A2.946,2.946,0,0,0,17,9ZM9,7a3,3,0,0,1,6,0V9H9Zm4.1,8.5-.1.1V17a1,1,0,0,1-2,0V15.6a1.487,1.487,0,1,1,2.1-.1Z"
                                                        transform="translate(-4 -2)" fill="#9d9da6" />
                                                </svg>
                                            @else
                                                <div class="form-group">
                                                    <div class="aiz-checkbox-inline">
                                                        <label class="aiz-checkbox">
                                                            <input type="checkbox" class="check-one" name="id[]"
                                                                value="{{ $notificationType->id }}">
                                                            <span class="aiz-square-check"></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <img src="{{ uploaded_asset($notificationType->image) }}"
                                                alt="{{ translate('Image') }}" class="h-30px">
                                        </td>
                                        <td class="fs-12 fw-700">{{ $notificationType->getTranslation('name') }}</td>
                                        <td class="fs-11">{{ $notificationType->getTranslation('default_text') }}</td>
                                        <td>
                                            <label class="aiz-switch aiz-switch-success mb-0">
                                                <input onchange="update_status(this)" 
                                                    value="{{ $notificationType->id }}"
                                                    type="checkbox" 
                                                    @if($notificationType->status == 1) checked @endif
                                                    @if(!auth()->user()->can('notification_types_status_update')) disabled @endif>
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                        <td class="text-right">
                                            @can('edit_notification_types')
                                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                                    href="{{ route('notification-type.edit', ['id' => $notificationType->id, 'lang' => env('DEFAULT_LANGUAGE')]) }}"
                                                    title="{{ translate('Edit') }}">
                                                    <i class="las la-edit"></i>
                                                </a>
                                            @endcan
                                            @if(auth()->user()->can('delete_notification_types') && $notificationType->type == 'custom')
                                                <a href="#"
                                                    class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                                    data-href="{{ route('notification-type.destroy', $notificationType->id) }}">
                                                    <i class="las la-trash"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="aiz-pagination">
                            {{ $notificationTypes->appends(request()->input())->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Notification Type Add --}}
        @can('add_notification_types')
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{ translate('Add New Notification Type') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('notification-type.store') }}" method="POST">
                            @csrf

                            <!-- Error Meassages -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group mb-3">
                                <label for="name">{{ translate('Type') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}"
                                    class="form-control" placeholder="{{ translate('Notification Type') }}" required>
                            </div>
                            <div class="form-group mb-3">
                                <label for="image">{{ translate('image') }} <small>(36x36)</small></label>
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">
                                            {{ translate('Browse') }}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="image" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label for="default_text">
                                    {{ translate('Default Text') }} <span class="text-danger">*</span>
                                    <br>
                                    <span
                                        class="fs-12 text-secondary fw-400">({{ translate('Best within 80 character') }})</span>
                                </label>
                                <textarea type="text" name="default_text" id="default_text" class="form-control"
                                    placeholder="{{ translate('Default Text') }}" required>{{ old('default_text') }}</textarea>
                                <small class="form-text text-danger">** {{ translate('N.B : Use character, number only') }}
                                    **</small>
                            </div>
                            <div class="form-group mb-3 text-right">
                                <button type="submit"
                                    class="btn btn-primary btn-sm fw-700 rounded-2 shadow-primary w-140px">{{ translate('Save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')
    <!-- Bulk Delete modal -->
    @include('modals.bulk_delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function sort_notification_types(value) {
            $('input[name="notification_user_type"]').val(value);
            $('#sort_notification_types').submit();
        }

        function update_status(el) {
            var status = el.checked ? 1 : 0;
            $.post('{{ route('notification-type.update-status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success',
                        '{{ translate('Notification type status updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        $(document).on("change", ".check-all", function() {
            $('.check-one:checkbox').prop('checked', this.checked);
        });

        function bulk_delete() {
            let notificationTypeIds = [];
            $(".check-one[name='id[]']:checked").each(function() {
                notificationTypeIds.push($(this).val());
            });
            $.post('{{ route('notifications-type.bulk_delete') }}', {
                _token: '{{ csrf_token() }}',
                notification_type_ids: notificationTypeIds
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Notification types deleted successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
                location.reload();
            });
        }
    </script>
@endsection
