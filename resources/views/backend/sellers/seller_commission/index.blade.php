@extends('backend.layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6 text-center">{{translate('Seller Commission Activatation')}}</h3>
                </div>
                <div class="card-body text-center">
                    <label class="aiz-switch aiz-switch-success mb-0">
                        <input type="checkbox" onchange="updateSettings(this, 'vendor_commission_activation')" <?php if(get_setting('vendor_commission_activation') == 1) echo "checked";?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Note')}}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item text-muted">
                            1. {{ translate('If the Commission Type is Fixed Rate') }}, {{ get_setting('vendor_commission') }}% {{translate('of seller product price will be deducted from seller earnings') }}.
                        </li>
                        <li class="list-group-item text-muted">
                            2. {{ translate('If the Commission Type is Seller Based, set commission percentage ') }} <a href="{{ route('sellers.index') }}">{{ translate('Here') }}</a>
                        </li>
                        <li class="list-group-item text-muted">
                            3. {{ translate('If the Commission Type is Category Based, set commission percentage ') }} <a href="{{ route('categories.index') }}">{{ translate('Here') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6 text-center">{{translate('Commission Type')}}</h3>
                </div>
                <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="types[]" value="seller_commission_type">
                    <div class="card-body">
                        <div class="radio mar-btm">
                            <input id="fixed_commission_rate" class="magic-radio" type="radio" name="seller_commission_type" value="fixed_rate" @if(get_setting('seller_commission_type') == 'fixed_rate') checked @endif>
                            <label for="fixed_commission_rate" class="fs-13">{{translate('Fixed Commission Rate')}}</label>
                        </div>
                        <div class="radio mar-btm">
                            <input id="seller_based_commission" class="magic-radio" type="radio" name="seller_commission_type" value="seller_based" @if(get_setting('seller_commission_type') == 'seller_based') checked @endif>
                            <label for="seller_based_commission" class="fs-13">{{translate('Seller Based Commission Rate')}}</label>
                        </div>
                        <div class="radio mar-btm">
                            <input id="category_based_commission" class="magic-radio" type="radio" name="seller_commission_type" value="category_based" @if(get_setting('seller_commission_type') == 'category_based') checked @endif>
                            <label for="category_based_commission" class="fs-13">{{translate('Category Based Commission Rate')}}</label>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(get_setting('seller_commission_type') == 'fixed_rate')
            <div class="col-lg-6">
                <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Fixed Commission Rate')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-md-4 col-from-label">{{translate('Seller Commission')}}</label>
                            <div class="col-md-8">
                                <input type="hidden" name="types[]" value="vendor_commission">
                                <div class="input-group">
                                    <input type="number" lang="en" min="0" step="0.01" value="{{ get_setting('vendor_commission') }}" placeholder="{{translate('Seller Commission')}}" name="vendor_commission" class="form-control">
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        @endif

        <div class="col-lg-6">
            <div class="card">
              <div class="card-header">
                  <h5 class="mb-0 h6">{{translate('Withdraw Seller Amount')}}</h5>
              </div>
              <div class="card-body">
                  <form class="form-horizontal" action="{{ route('business_settings.update') }}" method="POST" enctype="multipart/form-data">
                  	@csrf
                    <div class="form-group row">
                        <label class="col-md-4 col-from-label">{{translate('Minimum Seller Amount Withdraw')}}</label>
                        <div class="col-md-8">
                            <input type="hidden" name="types[]" value="minimum_seller_amount_withdraw">
                            <div class="input-group">
                                <input type="number" lang="en" min="0" step="0.01" value="{{ get_setting('minimum_seller_amount_withdraw') }}" placeholder="{{translate('Minimum Seller Amount Withdraw')}}" name="minimum_seller_amount_withdraw" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                    </div>
                  </form>
              </div>
            </div>
        </div>

    </div>

@endsection

@section('script')
    <script type="text/javascript">
        function updateSettings(el, type){

            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            if($(el).is(':checked')){
                var value = 1;
            }
            else{
                var value = 0;
            }

            $.post('{{ route('business_settings.update.activation') }}', {_token:'{{ csrf_token() }}', type:type, value:value}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        }
    </script>
@endsection
