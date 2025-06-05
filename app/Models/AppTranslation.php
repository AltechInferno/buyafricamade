<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class AppTranslation extends Model
{
    use PreventDemoModeChanges;

    use HasFactory;

    protected $guarded = [];
}
