<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class UserCoupon extends Model
{
    use HasFactory, PreventDemoModeChanges;
    public $timestamps = false;

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function coupon(){
    	return $this->belongsTo(Coupon::class);
    }
}
