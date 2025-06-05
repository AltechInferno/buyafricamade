<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\ClassifiedProductDetailCollection;
use App\Http\Resources\V2\ClassifiedProductMiniCollection;
use App\Models\CustomerProduct;
use App\Models\CustomerProductTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerProductController extends Controller
{
    public function all()
    {
        $products = CustomerProduct::where('status', '1')->where('published', '1')->paginate(10);
        return new ClassifiedProductMiniCollection($products);
    }

    public function ownProducts()
    {
        $products = CustomerProduct::where('user_id', auth()->user()->id)->paginate(20);
        return new ClassifiedProductMiniCollection($products);
    }

    public function relatedProducts($slug)
    {
        $product =   CustomerProduct::where('slug', $slug)->first();
        $products =   CustomerProduct::where('category_id', $product->category_id)->where('id', '!=', $product->id)->where('status', '1')->where('published', '1')->paginate(10);
        return new ClassifiedProductMiniCollection($products);
    }

    public function productDetails($slug)
    {
        return new ClassifiedProductDetailCollection(CustomerProduct::where('slug', $slug)->get());
    }

    public function store(Request $request)
    {   
        $user = auth()->user();

        if($user->remaining_uploads < 1){
            return response()->json([
                'result' => false,
                'message' => translate('Your classified product upload limit has been reached. Please update your package.')
            ]);
        }
        
        $customer_product                       = new CustomerProduct;
        $customer_product->name                 = $request->name;
        $customer_product->added_by             = $request->added_by;
        $customer_product->user_id              = $user->id;
        $customer_product->category_id          = $request->category_id;
        $customer_product->brand_id             = $request->brand_id;
        $customer_product->conditon             = $request->conditon;
        $customer_product->location             = $request->location;
        $customer_product->photos               = $request->photos;
        $customer_product->thumbnail_img        = $request->thumbnail_img;
        $customer_product->unit                 = $request->unit;

        $tags = array();
        if($request->tags[0] != null){
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }

        $customer_product->tags                 = implode(',', $tags);
        $customer_product->description          = $request->description;
        $customer_product->video_provider       = $request->video_provider;
        $customer_product->video_link           = $request->video_link;
        $customer_product->unit_price           = $request->unit_price;
        $customer_product->meta_title           = $request->meta_title;
        $customer_product->meta_description     = $request->meta_description;
        $customer_product->meta_img             = $request->meta_img;
        $customer_product->pdf                  = $request->pdf;
        $customer_product->slug                 = strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5));
        if($customer_product->save()){
            $user->remaining_uploads -= 1;
            $user->save();

            $customer_product_translation               = CustomerProductTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'customer_product_id' => $customer_product->id]);
            $customer_product_translation->name         = $request->name;
            $customer_product_translation->unit         = $request->unit;
            $customer_product_translation->description  = $request->description;
            $customer_product_translation->save();
            
            return response()->json([
                'result' => true,
                'message' => translate('Product has been added successfully.')
            ]);
        }

        return response()->json([
            'result' => false,
            'message' => translate('Something went wrong!')
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $customer_product                       = CustomerProduct::find($id);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $customer_product->name             = $request->name;
            $customer_product->unit             = $request->unit;
            $customer_product->description      = $request->description;
        }
        $customer_product->user_id              = $user->id;
        $customer_product->category_id          = $request->category_id;
        $customer_product->brand_id             = $request->brand_id;
        $customer_product->conditon             = $request->conditon;
        $customer_product->location             = $request->location;
        $customer_product->photos               = $request->photos;
        $customer_product->thumbnail_img        = $request->thumbnail_img;

        $tags = array();
        if($request->tags[0] != null){
            foreach (json_decode($request->tags[0]) as $key => $tag) {
                array_push($tags, $tag->value);
            }
        }

        $customer_product->tags                 = implode(',', $tags);
        $customer_product->video_provider       = $request->video_provider;
        $customer_product->video_link           = $request->video_link;
        $customer_product->unit_price           = $request->unit_price;
        $customer_product->meta_title           = $request->meta_title;
        $customer_product->meta_description     = $request->meta_description;
        $customer_product->meta_img             = $request->meta_img;
        $customer_product->pdf                  = $request->pdf;
        $customer_product->slug                 = strtolower($request->slug);
        if($customer_product->save()){

            $customer_product_translation               = CustomerProductTranslation::firstOrNew(['lang' => $request->lang, 'customer_product_id' => $customer_product->id]);
            $customer_product_translation->name         = $request->name;
            $customer_product_translation->unit         = $request->unit;
            $customer_product_translation->description  = $request->description;
            $customer_product_translation->save();

            return response()->json([
                'result' => true,
                'message' => translate('Product has been updated successfully.')
            ]);
        }

        return response()->json([
            'result' => false,
            'message' => translate('Something went wrong!')
        ]);
    }

    public function delete($id)
    {
        $product = CustomerProduct::where("id", $id)->where('user_id', auth()->user()->id)->delete();

        if($product)
            return response()->json(['result' => true, 'message' => translate('Product delete successfully')]);
        else
            return response()->json(['result' => false, 'message' => translate('Product delete failed')]);
    }

    public function changeStatus(Request $req, $id)
    {
        $product = CustomerProduct::where("id", $id)->where('user_id', auth()->user()->id)->first();

        $product->status = $req->status;
        $product->save();
        return response()->json([
            'result' => true,
            'message' => translate('Product has updated successfully')
        ]);
    }
}
