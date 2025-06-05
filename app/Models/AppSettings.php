<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class AppSettings extends Model
{
    use PreventDemoModeChanges;

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
