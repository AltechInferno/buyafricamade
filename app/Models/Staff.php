<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Staff extends Model
{
    use PreventDemoModeChanges;

    public function user()
    {
    return $this->belongsTo(User::class);
    }

    public function role()
    {
    return $this->belongsTo(Role::class);
    }

    public function pick_up_point()
    {
    	return $this->hasOne(PickupPoint::class);
    }

}
