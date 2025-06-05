@extends('seller.layouts.app')

@section('panel_content')

<div class="card">
    <div class="card-header row gutters-5">
        <div class="col">
            <h5 class="mb-0 h6">{{translate('Notifications')}}</h5>
        </div>
        <div class="col-md-3 text-right">
            <div class="btn-group mb-2">
                <button type="button" class="btn py-0" data-toggle="dropdown" aria-expanded="false">
                    <i class="las la-ellipsis-v"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <button onclick="bulk_notification_delete()" class="dropdown-item">{{ translate('Delete Selection') }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            <x-notification :notifications="$notifications" is_linkable is_deletable/>
        </ul>

        {{ $notifications->links() }}
        
    </div>
</div>

@endsection


@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            $('.check-one:checkbox').prop('checked', this.checked);
        });

        function bulk_notification_delete() {
            let notificationIds = [];
            $(".check-one[name='id[]']:checked").each(function() {
                notificationIds.push($(this).val());
            });
            $.post('{{ route('seller.notifications.bulk_delete') }}', {_token:'{{ csrf_token() }}', notification_ids:notificationIds}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Notification Deleted successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
                location.reload();
            });
        }
    </script>
@endsection



