@extends('backend.layouts.app')

@section('content')

    <div class="card">
        <form class="" action="" id="sort_orders" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6">{{ translate('All Orders') }}</h5>
                </div>

                @canany(['delete_order', 'export_order'])
                    <div class="dropdown mb-2 mb-md-0">
                        <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                            {{ translate('Bulk Action') }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            @can('delete_order')
                                <a class="dropdown-item confirm-alert" href="javascript:void(0)"  data-target="#bulk-delete-modal">{{ translate('Delete selection') }}</a>
                            @endcan
                            @can('export_order')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="order_bulk_export()">{{ translate('Export') }}</a>
                            @endcan
                            @if(auth()->user()->can('unpaid_order_payment_notification_send') && $unpaid_order_payment_notification->status == 1 && Route::currentRouteName() == 'unpaid_orders.index')
                                <a class="dropdown-item" href="javascript:void(0)" onclick="bulk_unpaid_order_payment_notification()">{{ translate('Unpaid Order Payment Notification') }}</a>
                            @endif
                        </div>
                    </div>
                @endcan
                @if(Route::currentRouteName() == 'offline_payment_orders.index')
                    <div class="col-lg-2 ml-auto">
                        <select class="form-control aiz-selectpicker" name="order_type" id="order_type">
                            <option value="">{{ translate('Filter by Order Type') }}</option>
                            <option value="inhouse_orders" @if ($order_type == 'inhouse_orders') selected @endif>{{ translate('Inhouse Orders') }}</option>
                            <option value="seller_orders" @if ($order_type == 'seller_orders') selected @endif>{{ translate('Seller Orders') }}</option>
                        </select>
                    </div>
                @endif

                <div class="col-lg-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="delivery_status" id="delivery_status">
                        <option value="">{{ translate('Filter by Delivery Status') }}</option>
                        <option value="pending" @if ($delivery_status == 'pending') selected @endif>{{ translate('Pending') }}
                        </option>
                        <option value="confirmed" @if ($delivery_status == 'confirmed') selected @endif>
                            {{ translate('Confirmed') }}</option>
                        <option value="picked_up" @if ($delivery_status == 'picked_up') selected @endif>
                            {{ translate('Picked Up') }}</option>
                        <option value="on_the_way" @if ($delivery_status == 'on_the_way') selected @endif>
                            {{ translate('On The Way') }}</option>
                        <option value="delivered" @if ($delivery_status == 'delivered') selected @endif>
                            {{ translate('Delivered') }}</option>
                        <option value="cancelled" @if ($delivery_status == 'cancelled') selected @endif>
                            {{ translate('Cancel') }}</option>
                    </select>
                </div>
                @if(Route::currentRouteName() != 'unpaid_orders.index')
                    <div class="col-lg-2 ml-auto">
                        <select class="form-control aiz-selectpicker" name="payment_status" id="payment_status">
                            <option value="">{{ translate('Filter by Payment Status') }}</option>
                            <option value="paid"
                                @isset($payment_status) @if ($payment_status == 'paid') selected @endif @endisset>
                                {{ translate('Paid') }}</option>
                            <option value="unpaid"
                                @isset($payment_status) @if ($payment_status == 'unpaid') selected @endif @endisset>
                                {{ translate('Unpaid') }}</option>
                        </select>
                    </div>
                @endif
                <div class="col-lg-1">
                    <div class="form-group mb-0">
                        <input type="text" class="aiz-date-range form-control" value="{{ $date }}"
                            name="date" placeholder="{{ translate('Filter by date') }}" data-format="DD-MM-Y"
                            data-separator=" to " data-advanced-range="true" autocomplete="off">
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="search"
                            name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                            placeholder="{{ translate('Type Order code & hit Enter') }}">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            @if (auth()->user()->can('delete_order') || auth()->user()->can('export_order'))
                                <th>
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-all">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                </th>
                            @else
                                <th data-breakpoints="lg">#</th>
                            @endif

                            <th>{{ translate('Order Code') }}</th>
                            <th data-breakpoints="md">{{ translate('Num. of Products') }}</th>
                            <th data-breakpoints="md">{{ translate('Customer') }}</th>
                            <th data-breakpoints="md">{{ translate('Seller') }}</th>
                            <th data-breakpoints="md">{{ translate('Amount') }}</th>
                            <th data-breakpoints="md">{{ translate('Delivery Status') }}</th>
                            <th data-breakpoints="md">{{ translate('Payment method') }}</th>
                            <th data-breakpoints="md">{{ translate('Payment Status') }}</th>
                            @if (addon_is_activated('refund_request'))
                                <th>{{ translate('Refund') }}</th>
                            @endif
                            <th class="text-right" width="15%">{{ translate('options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $key => $order)
                            <tr>
                                @if (auth()->user()->can('delete_order') || auth()->user()->can('export_order'))
                                    <td>
                                        <div class="form-group">
                                            <div class="aiz-checkbox-inline">
                                                <label class="aiz-checkbox">
                                                    <input type="checkbox" class="check-one" name="id[]"
                                                        value="{{ $order->id }}">
                                                    <span class="aiz-square-check"></span>
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                @else
                                    <td>{{ $key + 1 + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                @endif
                                <td>
                                    {{ $order->code }}
                                    @if ($order->viewed == 0)
                                        <span class="badge badge-inline badge-info">{{ translate('New') }}</span>
                                    @endif
                                    @if (addon_is_activated('pos_system') && $order->order_from == 'pos')
                                        <span class="badge badge-inline badge-danger">{{ translate('POS') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ count($order->orderDetails) }}
                                </td>
                                <td>
                                    @if ($order->user != null)
                                        {{ $order->user->name }}
                                    @else
                                        Guest ({{ $order->guest_id }})
                                    @endif
                                </td>
                                <td>
                                    @if ($order->shop)
                                        {{ $order->shop->name }}
                                    @else
                                        {{ translate('Inhouse Order') }}
                                    @endif
                                </td>
                                <td>
                                    {{ single_price($order->grand_total) }}
                                </td>
                                <td>
                                    {{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}
                                </td>
                                <td>
                                    {{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}
                                </td>
                                <td>
                                    @if ($order->payment_status == 'paid')
                                        <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                    @else
                                        <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                    @endif
                                </td>
                                @if (addon_is_activated('refund_request'))
                                    <td>
                                        @if (count($order->refund_requests) > 0)
                                            {{ count($order->refund_requests) }} {{ translate('Refund') }}
                                        @else
                                            {{ translate('No Refund') }}
                                        @endif
                                    </td>
                                @endif
                                <td class="text-right">
                                    @if (addon_is_activated('pos_system') && $order->order_from == 'pos')
                                        <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                            href="{{ route('admin.invoice.thermal_printer', $order->id) }}" target="_blank"
                                            title="{{ translate('Thermal Printer') }}">
                                            <i class="las la-print"></i>
                                        </a>
                                    @endif
                                    @can('view_order_details')
                                        @php
                                            $order_detail_route = route('orders.show', encrypt($order->id));
                                            if (Route::currentRouteName() == 'seller_orders.index') {
                                                $order_detail_route = route('seller_orders.show', encrypt($order->id));
                                            } elseif (Route::currentRouteName() == 'pick_up_point.index') {
                                                $order_detail_route = route('pick_up_point.order_show', encrypt($order->id));
                                            }
                                            if (Route::currentRouteName() == 'inhouse_orders.index') {
                                                $order_detail_route = route('inhouse_orders.show', encrypt($order->id));
                                            }
                                        @endphp
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                            href="{{ $order_detail_route }}" title="{{ translate('View') }}">
                                            <i class="las la-eye"></i>
                                        </a>
                                    @endcan
                                    <a class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                        href="{{ route('invoice.download', $order->id) }}"
                                        title="{{ translate('Download Invoice') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                    @if(auth()->user()->can('unpaid_order_payment_notification_send') && $order->payment_status == 'unpaid' && $unpaid_order_payment_notification->status == 1)
                                        <a class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                            href="javascript:void();" onclick="unpaid_order_payment_notification('{{ $order->id }}');"
                                            title="{{ translate('Unpaid Order Payment Notification') }}">
                                            <i class="las la-bell"></i>
                                        </a>
                                    @endif
                                    @can('delete_order')
                                        <a href="#"
                                            class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                            data-href="{{ route('orders.destroy', $order->id) }}"
                                            title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="aiz-pagination">
                    {{ $orders->appends(request()->input())->links() }}
                </div>

            </div>
        </form>
    </div>
@endsection

@section('modal')
    <!-- Delete modal -->
    @include('modals.delete_modal')

    <!-- Bulk Delete modal -->
    @include('modals.bulk_delete_modal')

    {{-- Bulk Unpaid Order Payment Notification --}}
    <div id="complete_unpaid_order_payment" class="modal fade">
        <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content pb-2rem px-2rem">
                <div class="modal-header border-0">
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <form class="form-horizontal" action="{{ route('unpaid_order_payment_notification') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body text-center">
                        <input type="hidden" name="order_ids" value="" id="order_ids">
                        <p class="mt-2 mb-2 fs-16 fw-700">{{ translate('Are you sure to send notification for the selected orders?') }}</p>
                        <button type="submit" class="btn btn-warning rounded-2 mt-2 fs-13 fw-700 w-250px">{{ translate('Send Notification') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if (this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });
        
        function bulk_delete() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('bulk-order-delete') }}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }
        
        function order_bulk_export (){
            var url = '{{route('order-bulk-export')}}';
            $("#sort_orders").attr("action", url);
            $('#sort_orders').submit();
            $("#sort_orders").attr("action", '');
        }

        // Set Commission
        function unpaid_order_payment_notification(shop_id){
            var orderIds = [];
            orderIds.push(shop_id);
            $('#order_ids').val(orderIds);
            $('#complete_unpaid_order_payment').modal('show', {backdrop: 'static'});
        }

        // Set seller bulk commission
         function bulk_unpaid_order_payment_notification(){
            var orderIds = [];
            $(".check-one[name='id[]']:checked").each(function() {
                orderIds.push($(this).val());
            });
            if(orderIds.length > 0){
                $('#order_ids').val(orderIds);
                $('#complete_unpaid_order_payment').modal('show', {backdrop: 'static'});
            }
            else{
                AIZ.plugins.notify('danger', '{{ translate('Please Select Order first.') }}');
            }
         }
    </script>
@endsection
