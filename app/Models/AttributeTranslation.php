<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class AttributeTranslation extends Model
{
  use PreventDemoModeChanges;

  protected $fillable = ['name', 'lang', 'attribute_id'];

  public function attribute(){
    return $this->belongsTo(Attribute::class);
  }

}
