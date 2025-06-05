<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LastViewedProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $product = $data->product;
                $wholesale_product = ($product->wholesale_product == 1) ? true : false;
                return [
                    'id' => $product->id,
                    'slug' => $product->slug,
                    'name' => $product->getTranslation('name'),
                    'thumbnail_image' => $product->thumbnail_img == null ? "" : uploaded_asset($product->thumbnail_img),
                    'has_discount' => home_base_price($product, false) != home_discounted_base_price($product, false),
                    'discount' => "-" . discount_in_percentage($product) . "%",
                    'stroked_price' => home_base_price($product),
                    'main_price' => home_discounted_base_price($product),
                    'rating' => (float) $product->rating,
                    'sales' => (int) $product->num_of_sale,
                    'is_wholesale' => $wholesale_product,
                    'links' => [
                        'details' => route('products.show', $product->id),
                    ]
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
