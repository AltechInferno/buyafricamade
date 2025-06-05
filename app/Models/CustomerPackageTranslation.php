<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class CustomerPackageTranslation extends Model
{
  use PreventDemoModeChanges;

  protected $fillable = ['name', 'lang', 'customer_package_id'];

  public function customer_package(){
   return $this->belongsTo(CustomerPackage::class);
  }
}
