<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Customer extends Model
{
  use PreventDemoModeChanges;

    protected $fillable = [
      'user_id',
    ];
    public function user(){
    	return $this->belongsTo(User::class);
    }
}
