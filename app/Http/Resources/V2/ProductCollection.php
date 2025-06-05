<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                return [
                    'id' => $data->id,
                    'slug' => $data->slug,
                    'name' => $data->getTranslation('name'),
                    'photos' => explode(',', $data->photos),
                    'thumbnail_image' => uploaded_asset($data->thumbnail_img),
                    'base_price' => (float) home_base_price($data, false),
                    'base_discounted_price' => (float) home_discounted_base_price($data, false),
                    'todays_deal' => (int) $data->todays_deal,
                    'featured' => (int) $data->featured,
                    'unit' => $data->unit,
                    'discount' => (float) $data->discount,
                    'discount_type' => $data->discount_type,
                    'rating' => (float) $data->rating,
                    'sales' => (int) $data->num_of_sale,
                    'links' => [
                        'details' => route('products.show', $data->id),
                        'reviews' => route('api.reviews.index', $data->id),
                        'related' => route('products.related', $data->id),
                        'top_from_seller' => route('products.topFromSeller', $data->id)
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
