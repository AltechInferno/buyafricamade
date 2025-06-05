<?php

namespace App\Http\Controllers;

use App\Http\Requests\DynamicPopupRequest;
use App\Models\DynamicPopup;
use Illuminate\Http\Request;

class DynamicPopupController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_all_dynamic_popups'])->only('index');
        $this->middleware(['permission:add_dynamic_popups'])->only('create');
        $this->middleware(['permission:edit_dynamic_popups'])->only('edit');
        $this->middleware(['permission:delete_dynamic_popups'])->only('destroy');
        $this->middleware(['permission:publish_dynamic_popups'])->only('update_status');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search = null;
        $dynamic_popups = DynamicPopup::orderBy('id', 'asc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $dynamic_popups = $dynamic_popups->where('title', 'like', '%'.$sort_search.'%');
        }
        $dynamic_popups = $dynamic_popups->paginate(15);
        return view('backend.marketing.dynamic_popup.index', compact('dynamic_popups', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.marketing.dynamic_popup.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DynamicPopupRequest $request)
    {
        DynamicPopup::create($request->except('_token'));
        flash(translate('Dynamic Popup has been inserted successfully'))->success();
        return redirect()->route('dynamic-popups.index');
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
    public function edit(DynamicPopup $dynamic_popup)
    {
        return view('backend.marketing.dynamic_popup.edit', compact('dynamic_popup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DynamicPopupRequest $request, DynamicPopup $dynamic_popup)
    {
        if (!$request->has('show_subscribe_form')) {
            $request->request->add(['show_subscribe_form' => null]);
        }
        $dynamic_popup->update($request->except(['_token','_method']));
        flash(translate('Dynamic Popup has been updated successfully'))->success();
        return redirect()->route('dynamic-popups.index');
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
            flash(translate('This Dynamic Popup cannot be deleted'))->error();
            return redirect()->route('dynamic-popups.index');
        }
        DynamicPopup::destroy($id);
        flash(translate('Dynamic Popup has been deleted successfully'))->success();
        return redirect()->route('dynamic-popups.index');
    }
    
    public function bulk_dynamic_popup_delete(Request $request)
    {
        DynamicPopup::whereIn('id', $request->id)->delete();
        return 1;
    }
    
    public function update_status(Request $request)
    {
        $dynamic_popup = DynamicPopup::findOrFail($request->id);
        $dynamic_popup->status = $request->status;
        if($dynamic_popup->save()){
            return 1;
        }
        return 0;
    }
}
