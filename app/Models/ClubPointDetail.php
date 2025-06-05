<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class ClubPointDetail extends Model
{
    use PreventDemoModeChanges;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function club_point()
    {
        return $this->belongsTo(ClubPoint::class);
    }
}
