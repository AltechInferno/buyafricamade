<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationTypeRequest;
use App\Models\NotificationType;
use App\Models\NotificationTypeTranslation;
use DB;
use Illuminate\Http\Request;

class NotificationTypeController extends Controller
{

    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_notification_types'])->only('index');
        $this->middleware(['permission:edit_notification_types'])->only('edit');
        $this->middleware(['permission:delete_notification_types'])->only('destroy', 'bulkDelete');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Notification Types
        $notification_type_sort_search = (isset($request->notification_type_sort_search) && $request->notification_type_sort_search) ? $request->notification_type_sort_search : null;
        $notificationUserType = $request->notification_user_type == null ? 'customer' :  $request->notification_user_type;
        $notificationTypes = NotificationType::where('user_type', $notificationUserType);
        if ($notification_type_sort_search != null){
            $notificationTypes = $notificationTypes->where('name', 'like', '%' . $notification_type_sort_search . '%')
                ->orWhereHas('notificationTypeTranslations', function ($q) use ($notification_type_sort_search) {
                    $q->where('name', 'like', '%' . $notification_type_sort_search . '%');
                });
        }
        $notificationTypes = $notificationTypes->orderByRaw("FIELD(type , 'custom') ASC")->paginate(10);
        return view('backend.notification.notification_types.index', compact('notificationTypes', 'notification_type_sort_search', 'notificationUserType'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NotificationTypeRequest $request)
    {
        $notificationType = new NotificationType();
        $notificationType->type = 'custom';
        $notificationType->name = $request->name;
        $notificationType->image = $request->image;
        $notificationType->default_text = str_replace( array( '\'', '"', ',', ';','{', '}','\r', '\n' ), '', $request->default_text);

        if($notificationType->save()){

            $notificationTypeTranslation = NotificationTypeTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'notification_type_id' => $notificationType->id]);
            $notificationTypeTranslation->name = $request->name;
            $notificationTypeTranslation->default_text = $notificationType->default_text;
            $notificationTypeTranslation->save();

            flash(translate('New Notification Type has been added successfully'))->success();
            return back();
        }
        flash(translate('Something went wrong!'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang   = $request->lang;
        $notificationType  = NotificationType::findOrFail($id);
        return view('backend.notification.notification_types.edit', compact('notificationType','lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(NotificationTypeRequest $request, $id)
    {
        $notificationType = NotificationType::findOrFail($id);
        $notificationType->image = $request->image;
        $default_text = str_replace( array( '\'', '"', ',', ';','{', '}','\r', '\n' ), '', $request->default_text);
        if($request->lang == env("DEFAULT_LANGUAGE")){
            $notificationType->name = $request->name;
            $notificationType->default_text = $default_text;
        }
        $notificationType->save();

        $notificationTypeTranslation = NotificationTypeTranslation::firstOrNew(['lang' => $request->lang, 'notification_type_id' => $notificationType->id]);
        $notificationTypeTranslation->name = $request->name;
        $notificationTypeTranslation->default_text = $default_text;
        $notificationTypeTranslation->save();

        flash(translate('Notification Type has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notificationType = NotificationType::findOrFail($id);
        $notificationType->notificationTypeTranslations()->delete();
        DB::table('notifications')->where('notification_type_id',$notificationType->id)->delete();

        if (NotificationType::destroy($id)) {
            flash(translate('Notification Type has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function updateStatus(Request $request) {
        $notificationType = NotificationType::findOrFail($request->id);
        $notificationType->status = $request->status;
        $notificationType->save();
        return 1;
    }

    public function bulkDelete(Request $request){
        if($request->notification_type_ids != null){
            foreach($request->notification_type_ids as $notification_type_id){
                $notificationType = NotificationType::findOrFail($notification_type_id);
                $notificationType->notificationTypeTranslations()->delete();
                $notificationType->delete();
                DB::table('notifications')->where('notification_type_id',$notificationType->id)->delete();
            }
        }
        return 1;
    }

    public function getDefaulText(Request $request){
        return NotificationType::where('id',$request->id)->first()->getTranslation('default_text');
    }
}
