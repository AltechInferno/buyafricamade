<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class SmsTemplate extends Model
{
    use PreventDemoModeChanges;

    protected $guarded = [];
}
