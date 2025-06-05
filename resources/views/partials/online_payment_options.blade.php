@foreach (get_activate_payment_methods() as $payment_method)
    <option value="{{ $payment_method->name }}">{{ ucfirst(translate($payment_method->name)) }}</option>
@endforeach
