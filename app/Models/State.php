<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class State extends Model
{
    use HasFactory,PreventDemoModeChanges;


    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function cities(){
        return $this->hasMany(City::class);
    }
}
