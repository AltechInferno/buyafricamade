<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class PasswordReset extends Model
{
    use PreventDemoModeChanges;

    protected $fillable = ['email', 'token'];
}
