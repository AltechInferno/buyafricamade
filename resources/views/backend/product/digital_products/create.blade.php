@extends('backend.layouts.app')

@section('content')
    <div class="page-content mx-0">
        <div class="aiz-titlebar text-left mt-2 mb-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="h3">{{ translate('Add New Digital Product') }}</h1>
                </div>
                <div class="col text-right">
                    <a class="btn btn-xs btn-soft-primary" href="javascript:void(0);" onclick="clearTempdata()">
                        {{ translate('Clear Tempdata') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="">
            <!-- Error Meassages -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Data type -->
        <input type="hidden" id="data_type" value="digital">
    
        <form class="form form-horizontal mar-top" action="{{ route('digitalproducts.store') }}" method="POST"
            enctype="multipart/form-data" id="choice_form">
            <div class="row gutters-5">
                <div class="col-lg-8">
                    @csrf
                    <input type="hidden" name="added_by" value="admin">
                    <input type="hidden" name="digital" value="1">
    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('General') }}</h5>
                        </div>
    
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-2 col-from-label">{{ translate('Product Name') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="{{ translate('Product Name') }}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-from-label">{{ translate('Product File') }}</label>
                                <div class="col-lg-8">
                                    <div class="input-group" data-toggle="aizuploader" data-multiple="false">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="file_name" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-from-label">{{ translate('Tags') }}</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control aiz-tag-input" name="tags[]"
                                        placeholder="{{ translate('Type to add a tag') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Images') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label"
                                    for="signinSrEmail">{{ translate('Main Images') }}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="photos" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
    
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="signinSrEmail">{{ translate('Thumbnail Image') }}
                                    <small>(290x300)</small></label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="thumbnail_img" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Meta Tags') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-2 col-from-label">{{ translate('Meta Title') }}</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="meta_title"
                                        placeholder="{{ translate('Meta Title') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-from-label">{{ translate('Description') }}</label>
                                <div class="col-lg-8">
                                    <textarea name="meta_description" rows="5" class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label"
                                    for="signinSrEmail">{{ translate('Meta Image') }}</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image"
                                        data-multiple="false">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="meta_img" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Price') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-2 col-from-label">{{ translate('Unit price') }} <span class="text-danger">*</span></label>
                                <div class="col-lg-8">
                                    <input type="number" lang="en" min="0" value="0" step="0.01"
                                        placeholder="{{ translate('Unit price') }}" name="unit_price" class="form-control"
                                        required>
                                </div>
                            </div>
                            @foreach (\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                                <div class="form-group row">
                                    <label class="col-lg-2 col-from-label">
                                        {{ $tax->name }}
                                    </label>
                                    <div class="col-lg-6">
                                        <input type="hidden" value="{{$tax->id}}" name="tax_id[]">
                                        <input type="number" lang="en" min="0" value="0" step="0.01"
                                            placeholder="{{ translate('Tax') }}" name="tax[]" class="form-control"
                                            required>
                                    </div>
                                    <div class="col-md-2">
                                        <select class="form-control aiz-selectpicker" name="tax_type[]">
                                            <option value="amount">{{ translate('Flat') }}</option>
                                            <option value="percent">{{ translate('Percent') }}</option>
                                        </select>
                                    </div>
                                </div>
                            @endforeach
                            <div class="form-group row">
                                <label class="col-lg-2 control-label"
                                    for="start_date">{{ translate('Discount Date Range') }}</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control aiz-date-range" name="date_range"
                                        placeholder="{{ translate('Select Date') }}" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss"
                                        data-separator=" to " autocomplete="off">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-from-label">{{ translate('Discount') }}</label>
                                <div class="col-lg-6">
                                    <input type="number" lang="en" min="0" value="0" step="0.01"
                                        placeholder="{{ translate('Discount') }}" name="discount" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-2">
                                    <select class="form-control aiz-selectpicker" name="discount_type">
                                        <option value="amount">{{ translate('Flat') }}</option>
                                        <option value="percent">{{ translate('Percent') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Information') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-lg-2 col-from-label">{{ translate('Description') }}</label>
                                <div class="col-lg-9">
                                    <textarea class="aiz-text-editor" name="description"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    {{-- Frequently Bought Products --}}
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Frequently Bought') }}</h5>
                        </div>
                        <div class="w-100">
                            <div class="d-flex my-3"> 
                                <div class="align-items-center d-flex mar-btm ml-4 mr-5 radio">
                                    <input id="fq_bought_select_products" type="radio" name="frequently_bought_selection_type" value="product" onchange="fq_bought_product_selection_type()" checked >
                                    <label for="fq_bought_select_products" class="fs-14 fw-500 mb-0 ml-2">{{translate('Select Product')}}</label>
                                </div>
                                <div class="radio mar-btm mr-3 d-flex align-items-center">
                                    <input id="fq_bought_select_category" type="radio" name="frequently_bought_selection_type" value="category" onchange="fq_bought_product_selection_type()">
                                    <label for="fq_bought_select_category" class="fs-14 fw-500 mb-0 ml-2">{{translate('Select Category')}}</label>
                                </div>
                            </div>
    
                            <div class="px-3 px-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="fq_bought_select_product_div">
        
                                            <div id="selected-fq-bought-products">
        
                                            </div>
        
                                            <button 
                                                type="button" 
                                                class="btn btn-block border border-dashed hov-bg-soft-secondary fs-14 rounded-0 d-flex align-items-center justify-content-center"
                                                onclick="showFqBoughtProductModal()">
                                                <i class="las la-plus"></i>
                                                <span class="ml-2">{{ translate('Add More') }}</span>
                                            </button>
                                        </div>
        
                                        {{-- Select Category for Frequently Bought Product --}}
                                        <div class="fq_bought_select_category_div d-none">
                                            <div class="form-group row">
                                                <label class="col-md-2 col-from-label">{{translate('Category')}}</label>
                                                <div class="col-md-10">
                                                    <select class="form-control aiz-selectpicker" data-placeholder="{{ translate('Select a Category')}}" name="fq_bought_product_category_id" data-live-search="true" required>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                                                            @foreach ($category->childrenCategories as $childCategory)
                                                                @include('categories.child_category', ['child_category' => $childCategory])
                                                            @endforeach
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 h6">{{ translate('Product Category') }}</h5>
                            <h6 class="float-right fs-13 mb-0">
                                {{ translate('Select Main') }}
                                <span class="position-relative main-category-info-icon">
                                    <i class="las la-question-circle fs-18 text-info"></i>
                                    <span class="main-category-info bg-soft-info p-2 position-absolute d-none border">{{ translate('This will be used for commission based calculations and homepage category wise product Show.') }}</span>
                                </span>
                            </h6>
                        </div>
                        <div class="card-body ">
                            <div class="h-170px overflow-auto c-scrollbar-light">
                                <ul class="hummingbird-treeview-converter list-unstyled" data-checkbox-name="category_ids[]" data-radio-name="category_id">
                                    @foreach ($categories as $category)
                                    <li id="{{ $category->id }}">{{ $category->getTranslation('name') }}</li>
                                        @foreach ($category->childrenCategories as $childCategory)
                                            @include('backend.product.products.child_category', ['child_category' => $childCategory])
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="float-right mb-3">
                        <button type="submit" name="button" class="btn btn-primary">{{ translate('Save Product') }}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('modal')
	<!-- Frequently Bought Product Select Modal -->
    @include('modals.product_select_modal')
@endsection

@section('script')
    <!-- Treeview js -->
    <script src="{{ static_asset('assets/js/hummingbird-treeview.js') }}"></script>
    <script type="text/javascript">

        $(document).ready(function() {
            $("#treeview").hummingbird();
        });

        function fq_bought_product_selection_type(){
            var productSelectionType = $("input[name='frequently_bought_selection_type']:checked").val();
            if(productSelectionType == 'product'){
                $('.fq_bought_select_product_div').removeClass('d-none');
                $('.fq_bought_select_category_div').addClass('d-none');
            }
            else if(productSelectionType == 'category'){
                $('.fq_bought_select_category_div').removeClass('d-none');
                $('.fq_bought_select_product_div').addClass('d-none');
            }
        }

        function showFqBoughtProductModal() {
            $('#fq-bought-product-select-modal').modal('show', {backdrop: 'static'});
        }

        function filterFqBoughtProduct() {
            var searchKey = $('input[name=search_keyword]').val();
            var fqBroughCategory = $('select[name=fq_brough_category]').val();
            $.post('{{ route('product.search') }}', { _token: AIZ.data.csrf,  product_id: null, search_key:searchKey, category:fqBroughCategory, product_type:"digital" }, function(data){
                $('#product-list').html(data);
                AIZ.plugins.fooTable();
            });
        }

        function addFqBoughtProduct() {
            var selectedProducts = [];
            $("input:checkbox[name=fq_bought_product_id]:checked").each(function() {
                selectedProducts.push($(this).val());
            });

            var fqBoughtProductIds = [];
            $("input[name='fq_bought_product_ids[]']").each(function() {
                fqBoughtProductIds.push($(this).val());
            });

            var productIds = selectedProducts.concat(fqBoughtProductIds.filter((item) => selectedProducts.indexOf(item) < 0))

            $.post('{{ route('get-selected-products') }}', { _token: AIZ.data.csrf, product_ids:productIds}, function(data){
                $('#fq-bought-product-select-modal').modal('hide');
                $('#selected-fq-bought-products').html(data);
                AIZ.plugins.fooTable();
            });
        }

    </script>

    @include('partials.product.product_temp_data')

@endsection
