<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class FlashDealProduct extends Model
{
    use PreventDemoModeChanges;

    protected $fillable=['flash_deal_id', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
