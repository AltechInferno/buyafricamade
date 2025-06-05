@extends('backend.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header d-block d-sm-flex">
                <div>
                    <p class="fs-13 fw-700 mb-0">{{ translate('Custom Notification History') }}</p>
                    <small
                        class="fs-11">{{ translate('Default system notifications doesn’t show in custom notification history') }}</small>
                </div>
                @can('delete_custom_notification_history')
                    <div class="dropdown mb-md-0 mt-3 mt-sm-0">
                        <button class="btn btn-sm border dropdown-toggle text-secondary" type="button" data-toggle="dropdown">
                            {{ translate('Bulk Action') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item confirm-alert" href="javascript:void(0)" data-target="#bulk-delete-modal">
                                {{ translate('Delete selection') }}</a>
                        </div>
                    </div>
                @endcan
            </div>
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
                                <th>{{ translate('Type') }}</th>
                                <th>{{ translate('Date & Time ') }}</th>
                                <th width="35%">{{ translate('Notification') }}</th>
                                <th width="25%">{{ translate('Link') }}</th>
                                <th class="text-right" width="10%">{{ translate('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customNotifications as $key => $customNotification)
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <div class="aiz-checkbox-inline">
                                                <label class="aiz-checkbox">
                                                    <input type="checkbox" class="check-one" name="type_created_at[]"
                                                        value="{{ $customNotification->notification_type_id . '_' . $customNotification->created_at }}">
                                                    <span class="aiz-square-check"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fs-12 fw-700">
                                        {{ get_notification_type($customNotification->notification_type_id, 'id')->getTranslation('name') }}
                                    </td>
                                    <td class="fs-12">{{ date('d.m.Y,  g.i a', strtotime($customNotification->created_at)) }}</td>
                                    <td class="fs-11">
                                        {{ get_notification_type($customNotification->notification_type_id, 'id')->getTranslation('default_text') ?? null }}
                                    </td>
                                    <td class="fs-11">{{ json_decode($customNotification->data, true)['link'] }}</td>

                                    <td class="text-right">
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="javascript:void(0);"
                                            onclick="show_notified_customers_modal('{{ $customNotification->notification_type_id . '_' . $customNotification->created_at }}')"
                                            title="{{ translate('customers') }}">
                                            <i class="las la-users"></i>
                                        </a>
                                        @can('delete_custom_notification_history')
                                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                                data-href="{{ route('custom-notifications.delete', $customNotification->notification_type_id . '_' . $customNotification->created_at) }}">
                                                <i class="las la-trash"></i>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination customNotifications-pagination">
                        {{ $customNotifications->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')

    <!-- Bulk Delete modal -->
    @include('modals.bulk_delete_modal')

    <!-- Custom notification sent customers list show  Modal -->
    <div class="modal fade" id="notified_customers_list_modal">
        <div class="modal-dialog">
            <div class="modal-content" id="notified_customers_list_modal_content">

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            $('.check-one:checkbox').prop('checked', this.checked);
        });

        function bulk_delete() {
            let identifiers = [];
            $(".check-one[name='type_created_at[]']:checked").each(function() {
                identifiers.push($(this).val());
            });
            $.post('{{ route('custom-notifications.bulk_delete') }}', {
                _token: '{{ csrf_token() }}',
                identifiers: identifiers
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Custom Notification Deleted successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
                location.reload();
            });
        }

        function show_notified_customers_modal(identifier){
            $.post('{{ route('custom_notified_customers_list') }}',{_token:'{{ @csrf_token() }}', identifier:identifier}, function(data){
                $('#notified_customers_list_modal #notified_customers_list_modal_content').html(data);
                $('#notified_customers_list_modal').modal('show', {backdrop: 'static'});
            });
        }
    </script>
@endsection
