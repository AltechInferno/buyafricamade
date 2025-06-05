@if(count($products) > 0)
<table class="table table-bordered aiz-table">
    <tbody>
        @foreach ($products as $key => $product)
            <tr>
              <td class="py-2">
                <div class="from-group row align-items-center">
                  <div class="col-auto">
                    <label class="aiz-checkbox">
                      <input type="checkbox" class="check-one" name="fq_bought_product_id" value="{{ $product->id }}">
                      <span class="aiz-square-check"></span>
                    </label>
                    <img class="size-48px img-fit" src="{{ uploaded_asset($product->thumbnail_img)}}">
                  </div>
                  <div class="col">
                    <span>{{ $product->name  }}</span>
                  </div>
                </div>
              </td>
              <td class="py-2" style="vertical-align: middle;">
                  <span>{{ single_price($product->unit_price) }}</span>
              </td>
            </tr>
        @endforeach
    </tbody>
  </table>
@endif