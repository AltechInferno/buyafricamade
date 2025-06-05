<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class TicketReply extends Model
{
    use PreventDemoModeChanges;

    public function ticket(){
    	return $this->belongsTo(Ticket::class);
    }
    public function user(){
    	return $this->belongsTo(User::class);
    }
}
