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
</style>

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('Dynamic Popups')}}</h1>
		</div>
        @can('add_dynamic_popups')
            <div class="col-md-6 text-md-right">
                <a href="{{ route('dynamic-popups.create') }}" class="btn btn-circle btn-info">
                    <span>{{translate('Create New Dynamic Popup')}}</span>
                </a>
            </div>
        @endcan
	</div>
</div>

<div class="card">
    <form class="" id="sort_dynamic_popup" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-0 h6">{{translate('All Dynamic Popups')}}</h5>
            </div>

            @can('delete_dynamic_popups')
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
                        @can('delete_dynamic_popups')
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
                        <th data-breakpoints="lg">{{translate('Title')}}</th>
                        <th data-breakpoints="lg">{{translate('Link')}}</th>
                        <th data-breakpoints="lg">{{translate('Status')}}</th>
                        <th class="text-right">{{translate('Actions')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dynamic_popups as $dynamic_popup)
                    <tr>
                        @can('delete_dynamic_popups')
                            <td>
                                @if($dynamic_popup->id != 1)
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-one" name="id[]" value="{{$dynamic_popup->id}}">
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
                        @endcan
                        <td>
                            <div class="size-64px rounded-2 overflow-hidden">
                                <img class="h-100 img-fit" src="{{ uploaded_asset($dynamic_popup->banner) }}" alt="">
                            </div>
                        </td>
                        <td class="fs-13 fw-700">{{ $dynamic_popup->title }}</td>
                        <td>{{ $dynamic_popup->btn_link }}</td>
                        <td>
                            <label class="aiz-switch aiz-switch-primary mb-0">
								<input
                                    @can('publish_dynamic_popups') onchange="trigger_alert(this)" @endcan
                                    value="{{ $dynamic_popup->id }}" id="trigger_alert_{{ $dynamic_popup->id }}" type="checkbox" @if($dynamic_popup->status == 1) checked @endif
                                    @cannot('publish_dynamic_popups') disabled @endcan
                                >
								<span class="slider round"></span>
							</label>
                        </td>
                        <td>
                            <div class="dropdown float-right">
                                <button class="btn btn-light size-40px action-toggle dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-xs">
                                    @can('edit_dynamic_popups')
                                        <a class="dropdown-item" href="{{route('dynamic-popups.edit', $dynamic_popup->id)}}">
                                            {{translate('Edit')}}
                                        </a>
                                    @endcan
                                    @if($dynamic_popup->id != 1)
                                        @can('delete_dynamic_popups')
                                            <a class="dropdown-item confirm-delete" href="javascript:void(0)" data-href="{{route('dynamic-popups.destroy', $dynamic_popup->id)}}">
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
                {{ $dynamic_popups->appends(request()->input())->links() }}
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
                    <a href="javascript:void(0)" id="trigger_btn" data-value="" data-status="" data-clicked="" class="btn btn-warning rounded-2 mt-2 fs-13 fw-700 w-250px" onclick="update_dynamic_popup_status()"></a>
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
            var confirm_text = status == 1 ? "{{translate('Are you sure you want to trigger this Popup?')}}" : "{{translate('Are you sure you want to close this Popup?')}}";
            var confirm_detail_text = status == 1 ? "{{translate('Triggering this will show this Popup to all visiting customer immediately.')}}" : "{{translate('closing this will hide this Popup from all visiting customer immediately.')}}";
            var confirm_btn_text = status == 1 ? "{{translate('Trigger This Popup')}}" : "{{translate('Hide This Popup')}}";
            $('#trigger_btn').attr('data-value', id);
            $('#trigger_btn').attr('data-status', status);
            $('#trigger_btn').text(confirm_btn_text);
            $('#confirm_text').text(confirm_text);
            $('#confirm_detail_text').text(confirm_detail_text);
            $('#confirm-trigger-modal').modal('show');
        }

        function update_dynamic_popup_status(el){


            $('#trigger_btn').attr('data-clicked', 1);
            $('#confirm-trigger-modal').modal('hide');
            var id = $('#trigger_btn').attr('data-value');
            var status = $('#trigger_btn').attr('data-status');
            $.post('{{ route('dynamic-popups.update-status') }}', {_token:'{{ csrf_token() }}', id:id, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Dynamic popup status updated successfully') }}');
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
        })

        $(document).on("change", ".check-all", function() {
            $('.check-one:checkbox').prop('checked', this.checked);
        });

        function bulk_delete() {
            var data = new FormData($('#sort_dynamic_popup')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-dynamic-popup-delete')}}",
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
