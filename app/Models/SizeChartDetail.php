<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class SizeChartDetail extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];
}
