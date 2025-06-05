<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NotificationCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                $notificationType = get_notification_type($data->notification_type_id, 'id');
                $notifyContent = $notificationType->getTranslation('default_text');
                if ($data->type == 'App\Notifications\OrderNotification'){
                    $notifyContent = str_replace('[[order_code]]', $data->data['order_code'], $notifyContent);
                }
                return [
                    'id' => $data->id,
                    "isChecked" => false,
                    'type' => $data->type,
                    'data' => $data->data,
                    'notification_text' => $notifyContent,
                    'image' => uploaded_asset($notificationType->image),
                    'date' => date("F j Y, g:i a", strtotime($data->created_at))
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
