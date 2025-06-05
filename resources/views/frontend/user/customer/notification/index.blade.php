@extends('frontend.layouts.user_panel')

@section('panel_content')

<div class="card rounded-0 shadow-none border">
    <div class="card-header row gutters-5">
        <div class="col">
            <h5 class="mb-0 fs-20 fw-700 text-dark">{{translate('Notifications')}}</h5>
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
        <!-- Notifications -->
        <ul class="list-group list-group-flush">
            <div class="form-group">
                <div class="aiz-checkbox-inline">
                    <label class="aiz-checkbox">
                        <input type="checkbox" class="check-all">
                        <span class="aiz-square-check"></span>{{ translate('Select All') }}
                    </label>
                </div>
            </div>
            @php
                $notificationShowDesign = get_setting('notification_show_type');
                if($notificationShowDesign != 'only_text'){
                    $notifyImageDesign = '';
                    if($notificationShowDesign == 'design_2'){
                        $notifyImageDesign = 'rounded-1';
                    }
                    elseif($notificationShowDesign == 'design_3'){
                        $notifyImageDesign = 'rounded-circle';
                    }
                }
            @endphp
            @forelse($notifications as $notification)
                <li class="list-group-item d-flex justify-content-between align-items- py-3 px-0">
                    <div class="media text-inherit">
                        <div class="media-body">
                            <div class="d-flex">
                                @php
                                    $notificationType = get_notification_type($notification->notification_type_id, 'id');
                                    $notifyContent = $notificationType->getTranslation('default_text');
                                @endphp
                                <div class="form-group d-inline-block">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name='id[]' value="{{$notification->id}}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                                @if($notificationShowDesign != 'only_text')
                                    <div class="size-35px mr-2">
                                        <img
                                            src="{{ uploaded_asset($notificationType->image) }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/notification.png') }}';"
                                            class="img-fit h-100 {{ $notifyImageDesign }}" >
                                    </div>
                                @endif
                                <div>
                                    <p class="mb-1 text-truncate-2">
                                        @if($notification->type == 'App\Notifications\OrderNotification')
                                            @php
                                                $orderCode  = $notification->data['order_code'];
                                                    $route = route('purchase_history.details', encrypt($notification->data['order_id']));
                                                    $orderCode = "<a href='".$route."'>".$orderCode."</a>";
                                                $notifyContent = str_replace('[[order_code]]', $orderCode, $notifyContent);
                                            @endphp
                                        @elseif($notification->type == 'App\Notifications\CustomNotification')
                                            @php
                                                $link = $notification->data['link'];
                                                if($link != null){
                                                    $notifyContent = "<a href='".$link."'>".$notifyContent."</a>";
                                                }
                                            @endphp
                                        @endif
                                        {!! $notifyContent !!}
                                    </p>
                                    <small class="text-muted">
                                        {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <li class="list-group-item">
                    <div class="py-4 text-center fs-16">{{ translate('No notification found') }}</div>
                </li>
            @endforelse
        </ul>
        <!-- Pagination -->
        <div class="aiz-pagination mt-3">
            {{ $notifications->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')

    <!-- Rrder details modal -->
    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>

    <!-- Payment modal -->
    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="payment_modal_body">

                </div>
            </div>
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
            $.post('{{ route('notifications.bulk_delete') }}', {_token:'{{ csrf_token() }}', notification_ids:notificationIds}, function(data){
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
