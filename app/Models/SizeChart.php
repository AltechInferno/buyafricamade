<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class SizeChart extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];
    
    public function sizeChartDetails()
    {
        return $this->hasMany(SizeChartDetail::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
