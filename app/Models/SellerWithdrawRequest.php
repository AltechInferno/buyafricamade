<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class SellerWithdrawRequest extends Model
{
    use PreventDemoModeChanges;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
