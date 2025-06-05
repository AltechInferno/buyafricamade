<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Address extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['set_default'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    public function state()
    {
        return $this->belongsTo(State::class);
    }
    
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
