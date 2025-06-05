@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Update Your Product') }}</h1>
            </div>
        </div>
    </div>
    
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

    <form class="" action="{{route('seller.digitalproducts.update', $product->id)}}" method="POST" enctype="multipart/form-data">
        <input name="_method" type="hidden" value="Post">
        <input type="hidden" name="id" value="{{ $product->id }}">
        <input type="hidden" name="lang" value="{{ $lang }}">
        
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <ul class="nav nav-tabs nav-fill language-bar">
                        @foreach (get_all_active_language() as $key => $language)
                        <li class="nav-item">
                            <a class="nav-link text-reset @if ($language->code == $lang) active @endif py-3" href="{{ route('seller.digitalproducts.edit', ['id'=>$product->id, 'lang'=> $language->code] ) }}">
                                <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                                <span>{{$language->name}}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Product Name')}} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="name" placeholder="{{translate('Product Name')}}" value="{{$product->getTranslation('name', $lang)}}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Product File')}} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <div class="input-group" data-toggle="aizuploader" data-multiple="false">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="file_name" class="selected-files" value="{{ $product->file_name }}">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Tags')}} <span class="text-danger">*</span></label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control aiz-tag-input" name="tags[]" id="tags" value="{{ $product->tags }}" placeholder="{{ translate('Type to add a tag') }}">
                                <small class="text-muted">{{translate('This is used for search. Input those words by which cutomer can find this product.')}}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Images')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="signinSrEmail">{{translate('Gallery Images')}} <small>(600x600)</small></label>
                            <div class="col-lg-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="true">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="photos" value="{{ $product->photos }}" class="selected-files" required>
                                </div>
                                <div class="file-preview box sm">
                                </div>
                                <small class="text-muted">{{translate('These images are visible in product details page gallery. Use 600x600 sizes images.')}}</small>
                            </div>
                        </div>
        
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="signinSrEmail">{{translate('Thumbnail Image')}} <small>(300x300)</small></label>
                            <div class="col-lg-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="thumbnail_img" value="{{ $product->thumbnail_img }}" class="selected-files" required>
                                </div>
                                <div class="file-preview box sm">
                                </div>
                                <small class="text-muted">{{translate('This image is visible in all product box. Use 300x300 sizes image. Keep some blank space around main object of your image as we had to crop some edge in different devices to make it responsive.')}}</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Meta Tags')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Meta Title')}}</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="meta_title" value="{{ $product->meta_title }}" placeholder="{{translate('Meta Title')}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Description')}}</label>
                            <div class="col-lg-9">
                                <textarea name="meta_description" rows="8" class="form-control">{{ $product->meta_description }}</textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label" for="signinSrEmail">{{ translate('Meta Image') }}</label>
                            <div class="col-lg-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image" data-multiple="false">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="meta_img" value="{{ $product->meta_img }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Slug')}}</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" name="slug" value="{{ $product->slug }}" placeholder="{{translate('Slug')}}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 h6">{{translate('Price')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Unit price')}}</label>
                            <div class="col-lg-9">
                                <input type="text" placeholder="{{translate('Unit price')}}" name="unit_price" class="form-control" value="{{$product->unit_price}}" required>
                            </div>
                        </div>
                        @foreach (\App\Models\Tax::where('tax_status', 1)->get() as $tax)
                            @php
                                $tax_amount = 0;
                                $tax_type = '';
                                foreach ($tax->product_taxes as $row) {
                                    if ($product->id == $row->product_id) {
                                        $tax_amount = $row->tax;
                                        $tax_type = $row->tax_type;
                                    }
                                }
                            @endphp
                            <div class="form-group row">
                                <label class="col-lg-3 col-from-label">
                                    {{$tax->name}}
                                </label>
                                <div class="col-lg-6">
                                    <input type="hidden" value="{{$tax->id}}" name="tax_id[]">
                                    <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('tax')}}" name="tax[]" class="form-control" value="{{$tax_amount}}" required>
                                </div>
                                <div class="col-lg-3">
                                    <select class="form-control aiz-selectpicker" name="tax_type[]" required>
                                        <option value="amount" <?php if($tax_type == 'amount') echo "selected";?> >{{translate('Flat')}}</option>
                                        <option value="percent" <?php if($tax_type == 'percent') echo "selected";?> >{{translate('Percent')}}</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach

                        @php
                            $date_range = '';
                            if($product->discount_start_date){
                                $start_date = date('d-m-Y H:i:s', $product->discount_start_date);
                                $end_date = date('d-m-Y H:i:s', $product->discount_end_date);
                                $date_range = $start_date.' to '.$end_date;
                            }
                        @endphp

                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label" for="start_date">{{translate('Discount Date Range')}}</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control aiz-date-range" value="{{ $date_range }}" name="date_range" placeholder="{{translate('Select Date')}}" data-time-picker="true" data-format="DD-MM-Y HH:mm:ss" data-separator=" to " autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Discount')}}</label>
                            <div class="col-lg-6">
                                <input type="number" lang="en" min="0" step="0.01" placeholder="{{translate('Discount')}}" name="discount" class="form-control" value="{{ $product->discount }}" required>
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control aiz-selectpicker" name="discount_type" required>
                                    <option value="amount" <?php if($product->discount_type == 'amount') echo "selected";?> >{{translate('Flat')}}</option>
                                    <option value="percent" <?php if($product->discount_type == 'percent') echo "selected";?> >{{translate('Percent')}}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-lg-3 col-from-label">{{translate('Description')}}</label>
                            <div class="col-lg-9">
                                <textarea class="aiz-text-editor" name="description">{{ $product->getTranslation('description', $lang) }}</textarea>
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
                                <div class="radio mar-btm mr-5 ml-4 d-flex align-items-center">
                                    <input
                                        id="fq_bought_select_products"
                                        type="radio"
                                        name="frequently_bought_selection_type" 
                                        value="product" 
                                        onchange="fq_bought_product_selection_type()"
                                        @if($product->frequently_bought_selection_type == 'product') checked @endif
                                    >
                                    <label for="fq_bought_select_products" class="fs-14 fw-500 mb-0 ml-2">{{translate('Select Product')}}</label>
                                </div>
                                <div class="radio mar-btm mr-3 d-flex align-items-center">
                                    <input
                                        id="fq_bought_select_category"
                                        type="radio"
                                        name="frequently_bought_selection_type"
                                        value="category"
                                        onchange="fq_bought_product_selection_type()"
                                        @if($product->frequently_bought_selection_type == 'category') checked @endif
                                    >
                                    <label for="fq_bought_select_category" class="fs-14 fw-500 mb-0 ml-2">{{translate('Select Category')}}</label>
                                </div>
                            </div>
                            
                            <div class="px-3 px-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="fq_bought_select_product_div d-none">
                                            @php 
                                                $fq_bought_products = $product->frequently_bought_products()->where('category_id', null)->get();
                                            @endphp
            
                                            <div id="selected-fq-bought-products">
                                                @if(count($fq_bought_products) > 0)
                                                    <div class="table-responsive mb-4">
                                                        <table class="table aiz-table mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="opacity-50 pl-0">{{ translate('Product Thumb') }}</th>
                                                                    <th class="opacity-50">{{ translate('Product Name') }}</th>
                                                                    <th class="opacity-50">{{ translate('Category') }}</th>
                                                                    <th class="opacity-50 text-right pr-0">{{ translate('Options') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($fq_bought_products as $fQBproduct)
                                                                    <tr class="remove-parent">
                                                                        <input type="hidden" name="fq_bought_product_ids[]" value="{{ $fQBproduct->frequently_bought_product->id }}">
                                                                        <td class="w-150px pl-0" style="vertical-align: middle;">
                                                                            <p class="d-block size-48px">
                                                                                <img src="{{ uploaded_asset($fQBproduct->frequently_bought_product->thumbnail_img) }}" alt="{{ translate('Image')}}" 
                                                                                    class="h-100 img-fit lazyload" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                                                                            </p>
                                                                        </td>
                                                                        <td style="vertical-align: middle;">
                                                                            <p class="d-block fs-13 fw-700 hov-text-primary mb-1 text-dark" title="{{ translate('Product Name') }}">
                                                                                {{ $fQBproduct->frequently_bought_product->getTranslation('name') }}
                                                                            </p>
                                                                        </td>
                                                                        <td style="vertical-align: middle;">{{ $fQBproduct->frequently_bought_product->main_category->name ?? translate('Category Not Found') }}</td>
                                                                        <td class="text-right pr-0" style="vertical-align: middle;">
                                                                            <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                                                                <i class="las la-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
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
                                            @php 
                                                $fq_bought_product_category_id = $product->frequently_bought_products()->where('category_id','!=', null)->first(); 
                                                $fqCategory = $fq_bought_product_category_id != null ? $fq_bought_product_category_id->category_id : null;
                                                
                                            @endphp
                                            <div class="form-group row">
                                                <label class="col-md-2 col-from-label">{{translate('Category')}}</label>
                                                <div class="col-md-10">
                                                    <select
                                                        class="form-control aiz-selectpicker"
                                                        data-placeholder="{{ translate('Select a Category')}}"
                                                        name="fq_bought_product_category_id"
                                                        data-live-search="true"
                                                        data-selected="{{ $fqCategory }}"
                                                    >
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
                        <div class="h-190px overflow-auto c-scrollbar-light">
                            @php
                                $old_categories = $product->categories()->pluck('category_id')->toArray();
                            @endphp
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
        </div>

        
        <div class="form-group mb-0 text-right mb-2">
            <button type="submit" class="btn btn-primary">{{translate('Update Product')}}</button>
        </div>
    </form>

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
        var main_id = '{{ $product->category_id != null ? $product->category_id : 0 }}';
        var selected_ids = '{{ implode(",",$old_categories) }}';
        if (selected_ids != '') {
            const myArray = selected_ids.split(",");
            for (let i = 0; i < myArray.length; i++) {
                const element = myArray[i];
                $('#treeview input:checkbox#'+element).prop('checked',true);
                $('#treeview input:checkbox#'+element).parents( "ul" ).css( "display", "block" );
                $('#treeview input:checkbox#'+element).parents( "li" ).children('.las').removeClass( "la-plus" ).addClass('la-minus');
            }
        }
        $('#treeview input:radio[value='+main_id+']').prop('checked',true);
        fq_bought_product_selection_type();
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
        var productID = $('input[name=id]').val();
        var searchKey = $('input[name=search_keyword]').val();
        var fqBroughCategory = $('select[name=fq_brough_category]').val();
        $.post('{{ route('seller.product.search') }}', { _token: AIZ.data.csrf, product_id: productID, search_key:searchKey, category:fqBroughCategory, product_type:"digital" }, function(data){
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

        $.post('{{ route('seller.get-selected-products') }}', { _token: AIZ.data.csrf, product_ids:productIds}, function(data){
            $('#fq-bought-product-select-modal').modal('hide');
            $('#selected-fq-bought-products').html(data);
            AIZ.plugins.fooTable();
        });
    }

</script>
@endsection
