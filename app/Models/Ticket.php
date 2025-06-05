<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Ticket extends Model
{
    use PreventDemoModeChanges;

    public function user(){
    	return $this->belongsTo(User::class);
    }

    public function ticketreplies()
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at', 'desc');
    }

}
