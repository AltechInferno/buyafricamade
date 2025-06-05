<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Carrier extends Model
{
    use HasFactory, PreventDemoModeChanges;


    public function carrier_ranges(){
    	return $this->hasMany(CarrierRange::class);
    }
    
    public function carrier_range_prices(){
    	return $this->hasMany(CarrierRangePrice::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
