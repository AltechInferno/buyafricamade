<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Shop extends Model
{
  use PreventDemoModeChanges;


  protected $with = ['user'];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
  
  public function seller_package(){
    return $this->belongsTo(SellerPackage::class);
  }
  public function followers(){
    return $this->hasMany(FollowSeller::class);
  }
}
