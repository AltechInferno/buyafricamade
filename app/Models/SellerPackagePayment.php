<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class SellerPackagePayment extends Model
{
    use PreventDemoModeChanges;

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function seller_package(){
    	return $this->belongsTo(SellerPackage::class);
    }
}
