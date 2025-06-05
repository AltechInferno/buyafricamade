@extends('backend.layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Contacts') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0 " cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Name') }}</th>
                        <th >{{ translate('Email') }}</th>
                        <th data-breakpoints="lg">{{ translate('Phone') }}</th>
                        <th data-breakpoints="lg">{{ translate('Query') }}</th>
                        <th data-breakpoints="lg">{{ translate('Reply') }}</th>
                        <th>{{ translate('status') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contacts as $key => $contact)
                        <tr>
                            <td>{{ translate($key + 1) }}</td>
                            <td>{{ $contact->name }}</td>
                            <td>{{ $contact->email }}</td>
                            <td>{{ $contact->phone }}</td>
                            <td>{{ Str::limit($contact->content, 100) }}</td>
                            <td>{{ Str::limit($contact->reply, 100) }}</td>
                            <td>
                                <span
                                    class="badge badge-inline {{ $contact->reply == null ? 'badge-warning' : 'badge-success'  }}">
                                    {{ $contact->reply == null ? translate('Not Replied') : translate('Replied')}}
                                </span>
                            </td>
                            <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm"
                                    href="javascript:void(1)" onclick="showQuery({{ $contact->id }})"
                                    title="{{ translate('View') }}">
                                    <i class="las la-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $contacts->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
    <!-- Query Modal -->
	<div class="modal fade" id="query_modal">
	    <div class="modal-dialog">
	        <div class="modal-content" id="query-modal-content">

	        </div>
	    </div>
	</div>
    <!-- Reply Modal -->
    <div class="modal fade" id="reply_modal">
        <div class="modal-dialog">
            <div class="modal-content" id="reply-modal-content">

            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        function showQuery(id){
            $.post("{{ route('contact.query_modal') }}",{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#query_modal #query-modal-content').html(data);
                $('#query_modal').modal('show');
            });
        }
        function showReplyModal(id){
            $.post("{{ route('contact.reply_modal') }}",{_token:'{{ @csrf_token() }}', id:id}, function(data){
                $('#reply_modal #reply-modal-content').html(data);
                $('#reply_modal').modal('show');
                $('#query_modal').modal('hide');
            });
        }
    </script>
@endsection
