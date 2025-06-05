<?php

namespace App\Http\Controllers;

use App\Models\NotificationType;
use App\Models\User;
use App\Notifications\CustomNotification;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Notification;
use Validator;
use Redirect;

class NotificationController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:notification_settings'])->only('notificationSettings');
        $this->middleware(['permission:send_custom_notification'])->only('customNotification');
        $this->middleware(['permission:view_custom_notification_history'])->only('customNotificationHistory');
        $this->middleware(['permission:delete_custom_notification_history'])->only('customNotificationSingleDelete', 'customNotificationBulkDelete');
    }

    public function adminIndex()
    {
        $notifications = auth()->user()->notifications()->paginate(15);
        auth()->user()->unreadNotifications->markAsRead();
        return view('backend.notification.index', compact('notifications'));
    }

    public function customerIndex()
    {
        $notifications = auth()->user()->notifications()->paginate(15);
        auth()->user()->unreadNotifications->markAsRead();
        return view('frontend.user.customer.notification.index', compact('notifications'));
    }


    // Notification Settings
    public function notificationSettings(){
        return view('backend.notification.settings');
    }

    // Custom Notification
    public function customNotification(Request $request)
    {
        $customNotificationTypes = NotificationType::where('type','custom')->where('status',1)->get();
        $customers = User::where('user_type', 'customer')->where('email_verified_at', '!=', null)->where('banned',0)->get();
        return view('backend.notification.custom_notification', compact('customers', 'customNotificationTypes'));
    }

    // Custom Notification Send
    public function sendCustomNotification(Request $request)
    {
        $rules = [
            'user_ids'              => ['required'],
            'notification_type_id'  => ['required'],
            'link'                  => ['max:255'],
        ];
        $messages = [
            'user_ids.required'             => translate('Select Customers'),
            'notification_type_id.required' => translate('Notification type is required'),
            'link.max'                      => translate('Link should have max 255 characters')
        ];
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return Redirect::back()->withErrors($validator);
        }

        foreach($request->user_ids as $user_id){
            $user = User::where('id', $user_id)->first();
            $data = array();
            $data['link'] = $request->link;
            $data['notification_type_id'] = $request->notification_type_id;

            Notification::send($user, new CustomNotification($data));
        }
        flash(translate('Notification has been sent successfully'))->success();
        return back();
    }

    // Custom Notification History
    public function customNotificationHistory(){
        $customNotifications = DB::table('notifications')->where('type', 'App\Notifications\CustomNotification')
                            ->groupBy(DB::raw('Date(created_at)'), 'notification_type_id')
                            ->orderBy('created_at','desc')
                            ->paginate(13);

        return view('backend.notification.custom_notification_history', compact('customNotifications'));
    }

   

    // Notification delete
    public function bulkDeleteAdmin(Request $request){
        $this->bulkDelete($request->all());
        return 1;
    }

    public function bulkDeleteCustomer(Request $request){
        $this->bulkDelete($request->all());
        return 1;
    }

    public function bulkDelete($data){
        if($data['notification_ids']){
            foreach($data['notification_ids'] as $notificationId){
                DB::table('notifications')->where('id',$notificationId)->delete();
            }
        }
    }
    // Notification delete end

    // Notification marked as read redirect to the link
    public function readAndRedirect($id) {
        $userType = auth()->user()->user_type;
        $notificationId = decrypt($id);
        $notification = auth()->user()->unreadNotifications->where('id',$notificationId)->first();

        // Notification mark as read
        auth()->user()->unreadNotifications->where('id',$notificationId)->markAsRead();

        // Order notification redirect
        if($notification->type == 'App\Notifications\OrderNotification'){
            if($userType == 'admin'){
                return redirect()->route('all_orders.show', encrypt($notification->data['order_id']));
            }
            elseif($userType == 'seller'){
                return redirect()->route('seller.orders.show', encrypt($notification->data['order_id']));
            }
            elseif($userType == 'customer'){
                return redirect()->route('purchase_history.details', encrypt($notification->data['order_id']));
            }
        }

        // Shop Product notification redirect
        elseif($notification->type == 'App\Notifications\ShopProductNotification'){
            $productId     = $notification->data['id'];
            $productType   = $notification->data['type'];
            $lang           = env('DEFAULT_LANGUAGE');
            if($userType == 'admin'){
                if($productType == 'physical'){
                    return redirect()->route('products.seller.edit', ['id'=>$productId, 'lang'=>$lang]);
                }
                elseif($productType == 'digital'){
                    return redirect()->route('digitalproducts.edit', ['id'=>$productId, 'lang'=>$lang]);
                }
            }
            if($userType == 'seller'){
                if($productType == 'physical'){
                    return redirect()->route('seller.products.edit', ['id'=>$productId, 'lang'=>$lang]);
                }
                elseif($productType == 'digital'){
                    return redirect()->route('seller.digitalproducts.edit',  ['id'=>$productId, 'lang'=>$lang] );
                }
            }
        }

        // Shop Product notification redirect
        elseif($notification->type == 'App\Notifications\PayoutNotification'){
            $route = $userType == 'admin'
                    ? ( $notification->data['status'] == 'pending' ? 'withdraw_requests_all' : 'sellers.payment_histories')
                    : ( $notification->data['status'] == 'pending' ? 'seller.money_withdraw_requests.index' : 'seller.payments.index');
                
            return redirect()->route($route);
        }

        // Shop Verification notification redirect
        elseif($notification->type == 'App\Notifications\ShopVerificationNotification'){
            if($userType == 'admin' || $userType == 'staff'){
                return redirect()->route('sellers.show_verification_request', $notification->data['id']);
            }
            else{
                return redirect()->route('seller.dashboard');
            }
        }

        // Custom notification redirect
        elseif($notification->type == 'App\Notifications\CustomNotification'){
            return redirect()->to($notification->data['link']);
        }
     
    }

    // non Linkable custom Notification mark as Read and return total unread count
    public function nonLinkableNotificationRead(){
        $unReadNotifications = auth()->user()->notifications()->where('type', 'App\Notifications\customNotification')->get();
        foreach($unReadNotifications  as $notification){
            if($notification->data['link'] == null){
                $notification->read_at = date("Y-m-d H:i:s");
                $notification->save();
            }
        }
        return count(auth()->user()->unreadNotifications);
    }

    // Custom Notifications delete
    public function customNotificationSingleDelete($identifier) {
        $this->customNotificationDelete($identifier);
        flash(translate('Custom notification deleted successfully'))->success();
        return back();
    }

    public function customNotificationBulkDelete(Request $request) {
        if($request->identifiers != null){
            foreach($request->identifiers as $identifier){
                $this->customNotificationDelete($identifier);
            }
        }
        return 1;
    }

    public function customNotificationDelete($identifier){
        $var = explode("_", $identifier);
        $type = $var[0];
        $created_at = date('Y-m-d', strtotime($var[1]));
        DB::table('notifications')->where('notification_type_id', $type)->where(DB::raw('Date(created_at)'), $created_at)->delete();
    }
    // Custom Notifications delete end

    public function customNotifiedCustomersList(Request $request) {
        $var = explode("_", $request->identifier);
        $type = $var[0];
        $created_at = date('Y-m-d', strtotime($var[1]));
        $notifications = DB::table('notifications')->where('notification_type_id', $type)->where(DB::raw('Date(created_at)'), $created_at)->get();
        $notificationType = get_notification_type($notifications[0]->notification_type_id, 'id');
        $content = $notificationType->getTranslation('default_text');
        $notificationData = json_decode($notifications[0]->data, true);
        $link = json_decode($notifications[0]->data, true)['link'];
        return view('backend.notification.custom_notified_customers_list', compact('notifications', 'content', 'link'));
    }
}
