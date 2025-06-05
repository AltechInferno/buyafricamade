<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class AttributeCategory extends Model
{
    use PreventDemoModeChanges;

    //

    protected $table = "attribute_category";
}
