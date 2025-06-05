<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Review extends Model
{
  use PreventDemoModeChanges;

  public function user(){
    return $this->belongsTo(User::class);
  }

  public function product(){
    return $this->belongsTo(Product::class);
  }
}
