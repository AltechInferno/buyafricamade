<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class AttributeValue extends Model
{
    use PreventDemoModeChanges;


    public function attribute() {
        return $this->belongsTo(Attribute::class);
    }
}
