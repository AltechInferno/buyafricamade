<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Tax extends Model
{
    use PreventDemoModeChanges;

    public function product_taxes() {
        return $this->hasMany(ProductTax::class);
    }
}
