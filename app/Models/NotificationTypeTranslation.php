<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTypeTranslation extends Model
{
    protected $fillable = ['notification_type_id', 'name', 'default_text', 'lang'];

    public function notificationType(){
        return $this->belongsTo(NotificationType::class);
    }
}
