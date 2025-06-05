<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class RefundRequest extends Model
{
    use PreventDemoModeChanges;

    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
