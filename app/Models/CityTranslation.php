<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class CityTranslation extends Model
{
  use PreventDemoModeChanges;

  protected $fillable = ['name', 'lang', 'city_id'];

  public function city(){
    return $this->belongsTo(City::class);
  }
}
