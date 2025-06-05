<?php

namespace App\Http\Controllers\Api\V2;
use App\Http\Resources\V2\NotificationCollection;
use DB;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function allNotification()
    {
        auth()->user()->unreadNotifications->markAsRead();
        $notifications = auth()->user()->notifications()->get();
        return new NotificationCollection($notifications);
    }

    public function unreadNotifications(){
        $notifications = auth()->user()->unreadNotifications()->get();
        return response()->json([
            'count' => $notifications->count(),
            'data' => new NotificationCollection($notifications),
        ]);
    }

    public function bulkDelete(Request $request){
        if($request->notification_ids != null){
            $idsString = substr($request->notification_ids, strpos($request->notification_ids, '[') + 1, strpos($request->notification_ids, ']') - strpos($request->notification_ids, '[') - 1);
            $idsString = str_replace(' ', '', $idsString);
            $idsArray = explode(',', $idsString);
            $idsArray = array_map('trim', $idsArray);
            // dd($idsArray);

            foreach($idsArray as $notificationId){
                DB::table('notifications')->where('id',$notificationId)->delete();
            }
            return $this->success(translate('Notification deleted successfully'));
        }
        return  $this->failed(translate('Something went wrong'));
    }

    public function notificationMarkAsRead($notificationId) {
        $notification = auth()->user()->unreadNotifications->where('id',$notificationId)->first();

        // Notification mark as read
        auth()->user()->unreadNotifications->where('id',$notificationId)->markAsRead();

        return response()->json([
            'result' => false,
            'type' => $notification->type,
            'data' => $notification->data
        ]);
    }

}
