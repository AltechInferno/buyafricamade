<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class RoleTranslation extends Model
{
  use PreventDemoModeChanges;

    protected $fillable = ['name', 'lang', 'role_id'];

    public function role(){
      return $this->belongsTo(Role::class);
    }
}
