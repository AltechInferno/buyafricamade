<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class BrandTranslation extends Model
{
  use PreventDemoModeChanges;

  protected $fillable = ['name', 'lang', 'brand_id'];

  public function brand(){
    return $this->belongsTo(Brand::class);
  }
}
