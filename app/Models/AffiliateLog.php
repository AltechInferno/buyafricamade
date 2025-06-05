<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class AffiliateLog extends Model
{
    use PreventDemoModeChanges;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order_detail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
