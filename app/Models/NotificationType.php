<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App;

class NotificationType extends Model
{
    public function getTranslation($field = '', $lang = false)
    {
        $lang = $lang == false ? App::getLocale() : $lang;
        $notificationTypeTtranslation = $this->notificationTypeTranslations->where('lang', $lang)->first();
        return $notificationTypeTtranslation != null ? $notificationTypeTtranslation->$field : $this->$field;
    }

    public function notificationTypeTranslations()
    {
        return $this->hasMany(NotificationTypeTranslation::class);
    }
}
