<div class="modal-header">
    <h5 class="modal-title h6">{{translate('Notified Customers')}}</h5>
    <button type="button" class="close" data-dismiss="modal">
    </button>
</div>
<div class="modal-body">
    <p>
        <span class="fs-14 fw-700">{{ translate('Notification Text').' : '}}</span>
        <span class="fs-13 fw-500">{{ $content }}</span>
    </p>
    @if($link != null)
        <p>
            <span class="fs-14 fw-700">{{ translate('Link').' : '}}</span>
            <span class="fs-13 fw-500">{{ $link }}</span>
        </p>
    @endif
    <table class="table table-striped table-bordered" >
        <thead>
            <tr>
                <th>{{ translate('Name') }}</th>
                <th>{{ translate('Email/Phone') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($notifications as $notification)
                @php
                    $user = \App\Models\User::where('id', $notification->notifiable_id)->first();
                @endphp
                @if($user != null)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email ?? $user->phone }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
