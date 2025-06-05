<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DbNotification extends Notification
{
    public function send($notifiable, $notificationData)
    { 
        $notification_id= $notificationData->id;
        $contents       = $notificationData->data;
        $className      = $notificationData->className;
        $notifiableClass= new $notificationData->className($contents);
        $notifyData     = $notifiableClass->toArray($notifiable);

        $notificationTypeID = $notifyData['notification_type_id'];
        $data = $notifyData['data'];
        unset($notifyData);

        return $notifiable->routeNotificationFor('database')->create([
            'id' => $notification_id,
            'notification_type_id' => $notificationTypeID,
            'notifiable_type'=> \Auth::user()->id,
            'type' => $className,
            'data' => $data,
            'read_at' => null,
        ]);
    }    

}
