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
            @if(count($products) > 0)
                @foreach($products as $product)
                    <tr class="remove-parent">
                        <input type="hidden" name="fq_bought_product_ids[]" value="{{ $product->id }}">
                        <td class="w-150px pl-0" style="vertical-align: middle;">
                            <p class="d-block size-48px">
                                <img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="{{ translate('Image')}}" 
                                    class="h-100 img-fit lazyload" onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';">
                            </p>
                        </td>
                        <td style="vertical-align: middle;">
                            <p class="d-block fs-13 fw-700 hov-text-primary mb-1 text-dark" title="{{ translate('Product Name') }}">
                                {{ $product->getTranslation('name') }}
                            </p>
                        </td>
                        <td style="vertical-align: middle;">{{ $product->main_category->name ?? translate('Category Not Found') }}</td>
                        <td class="text-right pr-0" style="vertical-align: middle;">
                            <button type="button" class="mt-1 btn btn-icon btn-circle btn-sm btn-soft-danger" data-toggle="remove-parent" data-parent=".remove-parent">
                                <i class="las la-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>



