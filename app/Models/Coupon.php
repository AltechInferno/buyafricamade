<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Coupon extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = [
        'user_id', 'type', 'code','details','discount', 'discount_type', 'start_date', 'end_date', 'status'
    ];

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function userCoupons()
    {
        return $this->hasMany(UserCoupon::class);
    }

    public function couponUsages()
    {
        return $this->hasMany(CouponUsage::class);
    }

}
