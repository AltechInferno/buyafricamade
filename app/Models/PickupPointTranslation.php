<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class PickupPointTranslation extends Model
{
  use PreventDemoModeChanges;

    protected $fillable = ['name', 'address', 'lang', 'pickup_point_id'];

    public function poickup_point(){
      return $this->belongsTo(PickupPoint::class);
    }
}
