<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;
use App;

class CustomerPackage extends Model
{
  use PreventDemoModeChanges;

    public function getTranslation($field = '', $lang = false){
      $lang = $lang == false ? App::getLocale() : $lang;
      $brand_translation = $this->hasMany(CustomerPackageTranslation::class)->where('lang', $lang)->first();
      return $brand_translation != null ? $brand_translation->$field : $this->$field;
    }

    public function customer_package_translations(){
      return $this->hasMany(CustomerPackageTranslation::class);
    }

    public function customer_package_payments()
    {
        return $this->hasMany(CustomerPackagePayment::class);
    
    }
}
