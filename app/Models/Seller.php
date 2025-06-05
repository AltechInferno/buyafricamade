<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Seller extends Model
{
  use PreventDemoModeChanges;


  protected $with = ['user', 'user.shop'];

  public function user(){
  	return $this->belongsTo(User::class);
  }

  public function payments(){
  	return $this->hasMany(Payment::class);
  }

  public function seller_package(){
    return $this->belongsTo(SellerPackage::class);
}
}
