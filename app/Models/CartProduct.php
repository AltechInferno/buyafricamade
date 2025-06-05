<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class CartProduct extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
