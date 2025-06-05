<form action="{{ route('contact.reply') }}" method="POST">
    @csrf
    <input type="hidden" name="contact_id" value="{{ $contact->id }}">
    <div class="modal-header">
        <h5 class="modal-title h6">{{translate('Reply to Query')}}</h5>
        <button type="button" class="close" data-dismiss="modal">
        </button>
    </div>
    <div class="modal-body">
        <!-- Name -->
        <div class="form-group">
            <label for="name" class="fs-12 fw-700 text-soft-dark">{{  translate('Name') }}</label>
            <input type="text" class="form-control rounded-0" value="{{$contact->name }}" placeholder="{{  translate('Enter Name') }}" name="name" required readonly>
        </div>
        <!-- Email -->
        <div class="form-group">
            <label for="email" class="fs-12 fw-700 text-soft-dark">{{  translate('Email') }}</label>
            <input type="email" class="form-control rounded-0" value="{{ $contact->email }}" placeholder="{{  translate('Enter Email') }}" name="email" required readonly>
        </div>
        <!-- Reply -->
        <div class="form-group">
            <label for="reply" class="fs-12 fw-700 text-soft-dark">{{  translate('Reply for the query') }}</label>
            <textarea
                class="form-control rounded-0"
                placeholder="{{translate('Type here...')}}"
                name="reply"
                rows="3"
                required
            ></textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary">{{translate('Send')}}</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
    </div>
</form>
