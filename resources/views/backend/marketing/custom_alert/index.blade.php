@extends('backend.layouts.app')

@section('content')

<style>
    .aiz-table tr td,
    .aiz-table thead th {
        vertical-align: middle;
        padding: 0.5rem;
    }

    .action-toggle.dropdown-toggle::after {
        margin-left: auto;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
    }
    .aiz-megabox .aiz-megabox-elem {
    border: 3px solid #e9e9ec;
}
</style>

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('Custom Alerts')}}</h1>
		</div>
        @can('add_custom_alerts')
            <div class="col-md-6 text-md-right">
                <a href="{{ route('custom-alerts.create') }}" class="btn btn-circle btn-info">
                    <span>{{translate('Create New Custom Alert')}}</span>
                </a>
            </div>
        @endcan
	</div>
</div>

<div class="card mt-4">
    <div class="card-body border" style="background: #fcfcfc;">
        <h4 class="fs-16 fw-700">{{translate('Select Alert Location')}}</h4>
        <p class="fs-14">{{translate('Select any alert location from following')}}</p>
        <div class="row">
            <!-- From Bottom-left -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 my-3">
                <label class="aiz-megabox mb-3">
                    <input value="bottom-left" class="custom_alert_location" type="radio" onchange="confirm_alert_location('bottom-left')" @if(get_setting('custom_alert_location') == 'bottom-left') checked @endif>
                    <span class="d-block aiz-megabox-elem rounded-0">
                        <div class="h-120px w-180px overflow-hidden">
                            <img src="{{ static_asset('assets/img/alerts/alert-bottom-left.png') }}" class="h-100 w-100" alt="alert">
                        </div>
                    </span>
                </label>
                <br>
                <span class="fs-14 fw-500 text-dark">{{ translate('From Bottom-left') }}</span>
            </div>
            <!-- From Bottom-right -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 my-3">
                <label class="aiz-megabox mb-3">
                    <input value="bottom-right" class="custom_alert_location" type="radio" onchange="confirm_alert_location('bottom-right')" @if(get_setting('custom_alert_location') == 'bottom-right') checked @endif>
                    <span class="d-block aiz-megabox-elem rounded-0">
                        <div class="h-120px w-180px overflow-hidden">
                            <img src="{{ static_asset('assets/img/alerts/alert-bottom-right.png') }}" class="h-100 w-100" alt="alert">
                        </div>
                    </span>
                </label>
                <br>
                <span class="fs-14 fw-500 text-dark">{{ translate('From Bottom-right') }}</span>
            </div>
            <!-- From Top-left -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 my-3">
                <label class="aiz-megabox mb-3">
                    <input value="top-left" class="custom_alert_location" type="radio" onchange="confirm_alert_location('top-left')" @if(get_setting('custom_alert_location') == 'top-left') checked @endif>
                    <span class="d-block aiz-megabox-elem rounded-0">
                        <div class="h-120px w-180px overflow-hidden">
                            <img src="{{ static_asset('assets/img/alerts/alert-top-left.png') }}" class="h-100 w-100" alt="alert">
                        </div>
                    </span>
                </label>
                <br>
                <span class="fs-14 fw-500 text-dark">{{ translate('From Top-left') }}</span>
            </div>
            <!-- From Top-right -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 my-3">
                <label class="aiz-megabox mb-3">
                    <input value="top-right" class="custom_alert_location" type="radio" onchange="confirm_alert_location('top-right')" @if(get_setting('custom_alert_location') == 'top-right') checked @endif>
                    <span class="d-block aiz-megabox-elem rounded-0">
                        <div class="h-120px w-180px overflow-hidden">
                            <img src="{{ static_asset('assets/img/alerts/alert-top-right.png') }}" class="h-100 w-100" alt="alert">
                        </div>
                    </span>
                </label>
                <br>
                <span class="fs-14 fw-500 text-dark">{{ translate('From Top-right') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_custom_alerts" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6">{{translate('All Custom Alerts')}}</h5>
            </div>

            @can('delete_custom_alerts')
                <div class="dropdown mb-2 mb-md-0">
                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                        {{translate('Bulk Action')}}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item confirm-alert" href="javascript:void(0)"  data-target="#bulk-delete-modal">{{translate('Delete selection')}}</a>
                    </div>
                </div>
            @endcan

            <div class="col-md-3">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                </div>
            </div>
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        @can('delete_custom_alerts')
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
                        @endcan
                        <th>{{translate('Image')}}</th>
                        <th data-breakpoints="lg">{{translate('Text')}}</th>
                        <th data-breakpoints="lg">{{translate('Link')}}</th>
                        <th data-breakpoints="lg">{{translate('Type')}}</th>
                        <th data-breakpoints="lg">{{translate('Trigger')}}</th>
                        <th class="text-right">{{translate('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($custom_alerts as $key => $custom_alert)
                    <tr>
                        @can('delete_custom_alerts')
                            <td>
                                @if($custom_alert->id != 1)
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-one" name="id[]" value="{{$custom_alert->id}}">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20" viewBox="0 0 16 20">
                                        <path id="df12b5039313fc3798dfa93cfb504acd" d="M17,9V7A5,5,0,0,0,7,7V9a2.946,2.946,0,0,0-3,3v7a2.946,2.946,0,0,0,3,3H17a2.946,2.946,0,0,0,3-3V12A2.946,2.946,0,0,0,17,9ZM9,7a3,3,0,0,1,6,0V9H9Zm4.1,8.5-.1.1V17a1,1,0,0,1-2,0V15.6a1.487,1.487,0,1,1,2.1-.1Z" transform="translate(-4 -2)" fill="#9d9da6"/>
                                    </svg>
                                @endif
                            </td>
                            </td>
                        @endcan
                        <td>
                            <div class="size-64px rounded-2 overflow-hidden">
                                <img class="h-100 img-fit" src="{{ uploaded_asset($custom_alert->banner) }}" alt="">
                            </div>
                        </td>
                        <td class="fs-13 fw-700">{!! $custom_alert->description !!}</td>
                        <td>{{ $custom_alert->link }}</td>
                        <td>
                            @if($custom_alert->id != 1)
                                @if($custom_alert->type == 'small')
                                    <span class="bg-primary text-white px-3 py-2 rounded-2">{{ translate('Small') }}</span>
                                @else
                                    <span class="bg-danger text-white px-3 py-2 rounded-2">{{ translate('Large') }}</span>
                                @endif
                            @else
                                <span class="bg-secondary text-white px-3 py-2 rounded-2">{{ translate('Default') }}</span>
                            @endif
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-primary mb-0">
								<input
                                    @can('publish_custom_alerts') onchange="trigger_alert(this)" @endcan
                                    value="{{ $custom_alert->id }}" id="trigger_alert_{{ $custom_alert->id }}" type="checkbox" @if($custom_alert->status == 1) checked @endif
                                    @cannot('publish_custom_alerts') disabled @endcan
                                >
								<span class="slider round"></span>
							</label>
                        </td>
                        <td>
                            <div class="dropdown float-right">
                                <button class="btn btn-light size-40px action-toggle dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-xs">
                                    @can('edit_custom_alerts')
                                        <a class="dropdown-item" href="{{route('custom-alerts.edit', $custom_alert->id)}}">
                                            {{translate('Edit')}}
                                        </a>
                                    @endcan
                                    @if($custom_alert->id != 1)
                                        @can('delete_custom_alerts')
                                            <a class="dropdown-item confirm-delete" href="javascript:void(0)" data-href="{{route('custom-alerts.destroy', $custom_alert->id)}}">
                                                {{translate('Delete')}}
                                            </a>
                                        @endcan
                                    @endif
                                </div>
                              </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination mt-3">
                {{ $custom_alerts->appends(request()->input())->links() }}
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

    <!-- confirm trigger Modal -->
    <div id="confirm-trigger-modal" class="modal fade">
        <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content p-2rem">
                <div class="modal-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="64" viewBox="0 0 72 64">
                        <g id="Octicons" transform="translate(-0.14 -1.02)">
                          <g id="alert" transform="translate(0.14 1.02)">
                            <path id="Shape" d="M40.159,3.309a4.623,4.623,0,0,0-7.981,0L.759,58.153a4.54,4.54,0,0,0,0,4.578A4.718,4.718,0,0,0,4.75,65.02H67.587a4.476,4.476,0,0,0,3.945-2.289,4.773,4.773,0,0,0,.046-4.578Zm.6,52.555H31.582V46.708h9.173Zm0-13.734H31.582V23.818h9.173Z" transform="translate(-0.14 -1.02)" fill="#ffc700" fill-rule="evenodd"/>
                          </g>
                        </g>
                    </svg>
                    <p class="mt-2 mb-2 fs-16 fw-700" id="confirm_text"></p>
                    <p class="fs-13" id="confirm_detail_text"></p>
                    <a href="javascript:void(0)" id="trigger_btn" data-value="" data-status="" data-clicked="" class="btn btn-warning rounded-2 mt-2 fs-13 fw-700 w-250px" onclick="update_custom_alert_status()"></a>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->

    <!-- confirm location Modal -->
    <div id="confirm-location-modal" class="modal fade">
        <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 540px;">
            <div class="modal-content p-2rem">
                <div class="modal-body text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="72" height="64" viewBox="0 0 72 64">
                        <g id="Octicons" transform="translate(-0.14 -1.02)">
                          <g id="alert" transform="translate(0.14 1.02)">
                            <path id="Shape" d="M40.159,3.309a4.623,4.623,0,0,0-7.981,0L.759,58.153a4.54,4.54,0,0,0,0,4.578A4.718,4.718,0,0,0,4.75,65.02H67.587a4.476,4.476,0,0,0,3.945-2.289,4.773,4.773,0,0,0,.046-4.578Zm.6,52.555H31.582V46.708h9.173Zm0-13.734H31.582V23.818h9.173Z" transform="translate(-0.14 -1.02)" fill="#ffc700" fill-rule="evenodd"/>
                          </g>
                        </g>
                    </svg>
                    <p class="mt-3 mb-3 fs-16 fw-700">{{translate('Are you sure you want to locate this Custom Alert?')}}</p>
                    <form action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="types[]" value="custom_alert_location">
                        <input type="hidden" name="custom_alert_location" value="">
                        <button type="button" class="btn btn-light rounded-2 mt-2 fs-13 fw-700 w-150px" data-dismiss="modal">{{ translate('Cancel') }}</button>
                        <button type="submit" class="btn btn-success rounded-2 mt-2 fs-13 fw-700 w-150px">{{translate('Confirm')}}</button>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- /.modal -->
@endsection

@section('script')
    <script type="text/javascript">
        function trigger_alert(el){

            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            var id = el.value;
            var status = el.checked ? 1 : 0;
            var confirm_text = status == 1 ? "{{translate('Are you sure you want to trigger this Alert?')}}" : "{{translate('Are you sure you want to close this Alert?')}}";
            var confirm_detail_text = status == 1 ? "{{translate('Triggering this will show this Alert to all visiting customer immediately.')}}" : "{{translate('closing this will hide this Alert from all visiting customer immediately.')}}";
            var confirm_btn_text = status == 1 ? "{{translate('Trigger This Alert')}}" : "{{translate('Hide This Alert')}}";
            $('#trigger_btn').attr('data-value', id);
            $('#trigger_btn').attr('data-status', status);
            $('#trigger_btn').text(confirm_btn_text);
            $('#confirm_text').text(confirm_text);
            $('#confirm_detail_text').text(confirm_detail_text);
            $('#confirm-trigger-modal').modal('show');
        }

        function update_custom_alert_status(){
            $('#trigger_btn').attr('data-clicked', 1);
            $('#confirm-trigger-modal').modal('hide');
            var id = $('#trigger_btn').attr('data-value');
            var status = $('#trigger_btn').attr('data-status');
            $.post('{{ route('custom-alerts.update-status') }}', {_token:'{{ csrf_token() }}', id:id, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Custom Alert status updated successfully') }}');
                }
            });
        }

        $('#confirm-trigger-modal').on('hidden.bs.modal', function () {
            if ($('#trigger_btn').attr('data-clicked') == 1) {
                $('#trigger_btn').attr('data-clicked', '');
            }else{
                var id = $('#trigger_btn').attr('data-value');
                var status = $('#trigger_btn').attr('data-status') == 1 ? false : true;
                $('#trigger_alert_'+id).prop('checked', status);
            }
        });

        function confirm_alert_location(value){
            $('input[name="custom_alert_location"]').val(value);
            $('#confirm-location-modal').modal('show');
        }

        $('#confirm-location-modal').on('hidden.bs.modal', function () {
            value = "{{ get_setting('custom_alert_location') }}";
            $('.custom_alert_location').prop('checked', false);
            $('.custom_alert_location[value="'+value+'"]').prop('checked', true);
        })

        $(document).on("change", ".check-all", function() {
            $('.check-one:checkbox').prop('checked', this.checked);
        });

        function bulk_delete() {
            var data = new FormData($('#sort_custom_alerts')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-custom-alerts-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }
    </script>
@endsection
