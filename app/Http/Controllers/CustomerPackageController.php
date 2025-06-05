<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerPackageRequest;
use Illuminate\Http\Request;
use App\Models\CustomerPackage;
use App\Models\CustomerPackageTranslation;
use App\Models\CustomerPackagePayment;
use Auth;
use Session;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class CustomerPackageController extends Controller
{
    public function __construct() {
        // Staff Permission Check
        $this->middleware(['permission:view_classified_packages'])->only('index');
        $this->middleware(['permission:edit_classified_package'])->only('edit');
        $this->middleware(['permission:delete_classified_package'])->only('destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_packages = CustomerPackage::all();
        return view('backend.customer.customer_packages.index', compact('customer_packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.customer.customer_packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerPackageRequest $request)
    {
        if ($request->amount == 0 && CustomerPackage::where('amount', 0)->first() != null) {
            flash(translate('You cannot Add more than one Free package'))->error();
            return back();
        }

        $customer_package = new CustomerPackage;
        $customer_package->name = $request->name;
        $customer_package->amount = $request->amount;
        $customer_package->product_upload = $request->product_upload;
        $customer_package->logo = $request->logo;

        $customer_package->save();

        $customer_package_translation = CustomerPackageTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'customer_package_id' => $customer_package->id]);
        $customer_package_translation->name = $request->name;
        $customer_package_translation->save();


        flash(translate('Package has been inserted successfully'))->success();
        return redirect()->route('customer_packages.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $customer_package = CustomerPackage::findOrFail($id);
        return view('backend.customer.customer_packages.edit', compact('customer_package', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerPackageRequest $request, $id)
    {
        $customer_package = CustomerPackage::findOrFail($id);
        if ($request->amount == 0 && CustomerPackage::where('amount', 0)->where('id', '!=', $id)->first() != null) {
            flash(translate('You cannot Add more than one Free package'))->error();
            return back();
        }
        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $customer_package->name = $request->name;
        }
        $customer_package->amount = $request->amount;
        $customer_package->product_upload = $request->product_upload;
        $customer_package->logo = $request->logo;

        $customer_package->save();

        $customer_package_translation = CustomerPackageTranslation::firstOrNew(['lang' => $request->lang, 'customer_package_id' => $customer_package->id]);
        $customer_package_translation->name = $request->name;
        $customer_package_translation->save();

        flash(translate('Package has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer_package = CustomerPackage::findOrFail($id);
        foreach ($customer_package->customer_package_translations as $key => $customer_package_translation) {
            $customer_package_translation->delete();
        }
        CustomerPackage::destroy($id);

        flash(translate('Package has been deleted successfully'))->success();
        return redirect()->route('customer_packages.index');
    }

    public function purchase_package(Request $request)
    {
        $data['customer_package_id'] = $request->customer_package_id;
        $data['payment_method'] = $request->payment_option;

        $request->session()->put('payment_type', 'customer_package_payment');
        $request->session()->put('payment_data', $data);

        $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);

        if ($customer_package->amount == 0) {
            $user = Auth::user();
            if ($user->customer_package_id != null) {
                flash(translate('You cannot purchase this package anymore.'))->warning();
                return back();
            }
            return $this->purchase_payment_done(Session::get('payment_data'), null);
        }

        $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $request->payment_option))) . "Controller";
        if (class_exists($decorator)) {
            return (new $decorator)->pay($request);
        }
    }

    public function purchase_payment_done($payment_data, $payment = null)
    {
        $customer_package_id = $payment_data['customer_package_id'];
        $user = auth()->user();
        $user->customer_package_id = $payment_data['customer_package_id'];
        $customer_package = CustomerPackage::findOrFail($customer_package_id);
        $user->remaining_uploads += $customer_package->product_upload;
        $user->save();

        $customer_package_payment = new CustomerPackagePayment;
        $customer_package_payment->user_id = $user->id;
        $customer_package_payment->customer_package_id = $customer_package_id;
        $customer_package_payment->amount = $customer_package->amount;
        $customer_package_payment->payment_method = $payment_data['payment_method'];
        $customer_package_payment->payment_details = $payment;
        $customer_package_payment->save();

        flash(translate('Package purchasing successful'))->success();
        return redirect()->route('dashboard');
    }

    public function purchase_package_offline(Request $request)
    {
        $customer_package = CustomerPackage::findOrFail($request->package_id);

        $customer_package_payment = new CustomerPackagePayment;
        $customer_package_payment->user_id = auth()->user()->id;
        $customer_package_payment->customer_package_id = $request->package_id;
        $customer_package_payment->amount = $customer_package->amount;
        $customer_package_payment->payment_method = $request->payment_option;
        $customer_package_payment->payment_details = $request->trx_id;
        $customer_package_payment->approval = 0;
        $customer_package_payment->offline_payment = 1;
        $customer_package_payment->reciept = ($request->photo == null) ? '' : $request->photo;
        $customer_package_payment->save();

        flash(translate('Offline payment has been done. Please wait for response.'))->success();
        return redirect()->route('customer_products.index');
    }
}
