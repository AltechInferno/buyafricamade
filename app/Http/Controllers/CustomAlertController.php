<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomAlertRequest;
use App\Models\CustomAlert;
use Illuminate\Http\Request;

class CustomAlertController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_custom_alerts'])->only('index');
        $this->middleware(['permission:add_custom_alerts'])->only('create');
        $this->middleware(['permission:edit_custom_alerts'])->only('edit');
        $this->middleware(['permission:delete_custom_alerts'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $custom_alerts = CustomAlert::orderBy('id', 'asc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $custom_alerts = $custom_alerts->where('description', 'like', '%'.$sort_search.'%');
        }
        $custom_alerts = $custom_alerts->paginate(15);
        return view('backend.marketing.custom_alert.index', compact('custom_alerts', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.marketing.custom_alert.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomAlertRequest $request)
    {
        CustomAlert::create($request->except('_token'));
        flash(translate('Custom Alert has been inserted successfully'))->success();
        return redirect()->route('custom-alerts.index');
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
    public function edit(CustomAlert $custom_alert)
    {
        return view('backend.marketing.custom_alert.edit', compact('custom_alert'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomAlertRequest $request, CustomAlert $custom_alert)
    {
        $custom_alert->update($request->except(['_token','_method']));
        flash(translate('Custom Alert has been updated successfully'))->success();
        return redirect()->route('custom-alerts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($id == 1) {
            flash(translate('This Custom Alert cannot be deleted'))->error();
            return redirect()->route('custom-alerts.index');
        }
        CustomAlert::destroy($id);
        flash(translate('Custom Alert has been deleted successfully'))->success();
        return redirect()->route('custom-alerts.index');
    }
    
    public function bulk_custom_alerts_delete(Request $request)
    {
        CustomAlert::whereIn('id', $request->id)->delete();
        return 1;
    }
    
    public function update_status(Request $request)
    {
        $custom_alert = CustomAlert::findOrFail($request->id);
        $custom_alert->status = $request->status;
        if($custom_alert->save()){
            return 1;
        }
        return 0;
    }
}
