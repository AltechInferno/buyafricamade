<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class DeliveryBoyPayment extends Model
{
    use HasFactory, PreventDemoModeChanges;


    public function user(){
    	return $this->belongsTo(User::class);
    }
}
