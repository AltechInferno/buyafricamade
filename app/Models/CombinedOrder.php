<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class CombinedOrder extends Model
{
    use PreventDemoModeChanges;

    public function orders(){
    	return $this->hasMany(Order::class);
    }

    public function user(){
    	return $this->belongsTo(User::class);
    }
}
