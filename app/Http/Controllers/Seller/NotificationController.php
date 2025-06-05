<?php

namespace App\Http\Controllers\Seller;
use Illuminate\Http\Request;
use DB;
class NotificationController extends Controller
{
    public function index() {
        $notifications = auth()->user()->notifications()->paginate(15);
        auth()->user()->unreadNotifications->markAsRead();
        
        return view('seller.notification.index', compact('notifications'));
    }

    public function bulkDelete(Request $request){
        if($request->notification_ids){
            foreach($request->notification_ids as $notificationId){
                DB::table('notifications')->where('id',$notificationId)->delete();
            }
        }
        return 1;
    }

    public function readAndRedirect($id) {
        $decorator = "App\Http\Controllers\NotificationController";
        return (new $decorator)->readAndRedirect($id);
    }
}
