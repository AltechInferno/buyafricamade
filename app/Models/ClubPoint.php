<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class ClubPoint extends Model
{
    use PreventDemoModeChanges;

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function order(){
    	return $this->belongsTo(Order::class);
    }

    public function club_point_details(){
    	return $this->hasMany(ClubPointDetail::class);
    }
}
