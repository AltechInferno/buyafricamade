<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class AuctionProductBid extends Model
{
    use PreventDemoModeChanges;

    public function product(){
    	return $this->belongsTo(Product::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
