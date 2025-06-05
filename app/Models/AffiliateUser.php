<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class AffiliateUser extends Model
{
  use PreventDemoModeChanges;

    //
    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function affiliate_payments()
    {
      return $this->hasMany(AffiliatePayment::class)->orderBy('created_at', 'desc')->paginate(12);
    }
}
