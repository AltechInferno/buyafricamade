<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\PreventDemoModeChanges;

class Conversation extends Model
{
    use PreventDemoModeChanges;

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function sender(){
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
