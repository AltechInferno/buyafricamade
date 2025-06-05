<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Zone extends Model
{
    use HasFactory,PreventDemoModeChanges;



    protected $fillable = ['name', 'status'];

    public function carrier_range_prices(){
    	return $this->hasMany(CarrierRangePrice::class);
    }
}
