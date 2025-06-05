<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CommissionHistory;
use App\Models\CustomerPackagePayment;
use App\Models\DeliveryBoyPayment;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Product;
use App\Models\RefundRequest;
use App\Models\SellerPackagePayment;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
class EarningReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:earning_report'])->only('index');
    }

    public function index()
    {
        // sale data
        $product_sales = Order::where('delivery_status', 'delivered')->groupBy('time')
                        ->select(DB::raw('SUM(grand_total) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'))
                        ->whereYear('created_at', Carbon::now()->year)
                        ->orderBy(DB::raw('MONTH(created_at)'), 'asc')
                        ->get();
        $total_product_sale_earning = Order::where('delivery_status', 'delivered')->sum('grand_total');

        $seller_subscriptions = array();
        $total_seller_subscriptions_earning = 0;
        if (addon_is_activated('seller_subscription')) {
            $seller_subscriptions = SellerPackagePayment::groupBy('time')
                                    ->select(DB::raw('SUM(amount) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'))
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->where('approval', 1)
                                    ->orderBy(DB::raw('MONTH(created_at)'), 'asc')
                                    ->get();
            $total_seller_subscriptions_earning = SellerPackagePayment::where('approval', 1)->sum('amount');
        }

        $customer_subscriptions = CustomerPackagePayment::groupBy('time')
                                ->select(DB::raw('SUM(amount) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'))
                                ->whereYear('created_at', Carbon::now()->year)
                                ->where('approval', 1)
                                ->orderBy(DB::raw('MONTH(created_at)'), 'asc')
                                ->get();
        $total_customer_subscriptions_earning = CustomerPackagePayment::where('approval', 1)->sum('amount');

        // Payouts data
        $seller_payments = Payment::groupBy('time')->where('payment_method','!=','Seller paid to admin')
                        ->select(DB::raw('SUM(amount) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'))
                        ->whereYear('created_at', Carbon::now()->year)
                        ->orderBy(DB::raw('MONTH(created_at)'), 'asc')
                        ->get();
        $total_seller_payment_amount = Payment::where('payment_method','!=','Seller paid to admin')->sum('amount');

        $refunds = array();
        $total_refund_amount = 0;
        if (addon_is_activated('refund_request')) {
            $refunds = RefundRequest::groupBy('time')
                        ->select(DB::raw('SUM(refund_amount) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'))
                        ->whereYear('created_at', Carbon::now()->year)
                        ->where('admin_approval', 1)
                        ->orderBy(DB::raw('MONTH(created_at)'), 'asc')
                        ->get();
            $total_refund_amount = RefundRequest::where('admin_approval', 1)->sum('refund_amount');
        }

        $delivery_boy_payments = array();
        $total_delivery_boy_payment_amount = 0;
        if (addon_is_activated('delivery_boy')) {
            $delivery_boy_payments = DeliveryBoyPayment::groupBy('time')
                                    ->select(DB::raw('SUM(payment) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'))
                                    ->whereYear('created_at', Carbon::now()->year)
                                    ->orderBy(DB::raw('MONTH(created_at)'), 'asc')
                                    ->get();
            $total_delivery_boy_payment_amount = DeliveryBoyPayment::sum('payment');
        }
        $mymonths = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        foreach ($mymonths as $month) {
            // Sales
            $sale_stat_data['time'] = $month;
            $sale_stat_data['total'] = 0;

            foreach ($product_sales as $product_sale) {
                if ($product_sale->time == $month)
                    $sale_stat_data['total'] += $product_sale->total;
            }

            foreach ($seller_subscriptions as $seller_subscription) {
                if ($seller_subscription->time == $month)
                    $sale_stat_data['total'] += $seller_subscription->total;
            }

            foreach ($customer_subscriptions as $customer_subscription) {
                if ($customer_subscription->time == $month)
                    $sale_stat_data['total'] += $customer_subscription->total;
            }

            $sale_stat_data['formatted_price'] = single_price($sale_stat_data['total']);
            $sale_data[] = $sale_stat_data;

            //Payouts
            $payout_stat_data['time'] = $month;
            $payout_stat_data['total'] = 0;

            foreach ($seller_payments as $seller_payment) {
                if ($seller_payment->time == $month)
                    $payout_stat_data['total'] += $seller_payment->total;
            }

            foreach ($refunds as $refund) {
                if ($refund->time == $month)
                    $payout_stat_data['total'] += $refund->total;
            }

            foreach ($delivery_boy_payments as $delivery_boy_payment) {
                if ($delivery_boy_payment->time == $month)
                    $payout_stat_data['total'] += $delivery_boy_payment->total;
            }

            $payout_stat_data['formatted_price'] = single_price($payout_stat_data['total']);
            $payout_data[] = $payout_stat_data;
        }
        $data['sales_stat'] = $sale_data;
        $data['payout_stat'] = $payout_data;

        // Total sale Alltime and This month Sales
        $sales_this_month = 0;
        foreach($data['sales_stat'] as $sale){
            if($sale['time'] == date('F'))
                $sales_this_month += $sale['total'];
        }
        $data['total_sales_alltime'] = $total_product_sale_earning + $total_seller_subscriptions_earning + $total_customer_subscriptions_earning;
        $data['sales_this_month'] = $sales_this_month;
        // Total sale Alltime and This month Sales end

        // Total payouts and This month payouts
        $payout_this_month = 0;
        foreach($data['payout_stat'] as $payout){

            if($payout['time'] == date('F'))
                $payout_this_month += $payout['total'];
        }
        $data['total_payouts'] = $total_seller_payment_amount + $total_refund_amount + $total_delivery_boy_payment_amount;
        $data['payout_this_month'] = $payout_this_month;
        // Total payouts and This month payouts end

        // Category wise Report
        $data['total_categories'] = Category::count();
        $data['top_categories'] = Product::select('categories.name', 'categories.id', DB::raw('SUM(grand_total) as total'))
            ->leftJoin('order_details', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->where('orders.delivery_status', 'delivered')
            ->groupBy('categories.id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

        // Brand wise Report
        $data['total_brands'] = Brand::count();
        $data['top_brands'] = Product::select('brands.name', 'brands.id', DB::raw('SUM(grand_total) as total'))
            ->leftJoin('order_details', 'order_details.product_id', '=', 'products.id')
            ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('brands', 'products.brand_id', '=', 'brands.id')
            ->where('orders.delivery_status', 'delivered')
            ->groupBy('brands.id')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->get();

        if(env('DEMO_MODE') === 'Off'){
            $this->packageAmountStoreIntoPackagePaymentTable();
        }

        return view('backend.reports.earning_payout_report', $data);
    }

    // Net Sales
    public function net_sales(Request $request)
    {
        $intervalType = $request->interval_type;
        // Commission
        $commission_query = CommissionHistory::query();
        if ($intervalType == 'DAY') {
            $commission_query->whereDate('created_at', Carbon::today());
        }
        elseif($intervalType == 'WEEK' || $intervalType == 'MONTH') {
            $day = $intervalType == 'WEEK' ? 7 : 30;
            $commission_query->whereDate('created_at', '>', Carbon::now()->subDays($day));
        }
        $commission_query = $commission_query->select(DB::raw('SUM(admin_commission) as total_commission'))->first();
        $data['commission'] = $commission_query->total_commission;

        // Earning from delivery
        $delivery_cost_query = OrderDetail::query();
        $delivery_cost_query->select(DB::raw('SUM(shipping_cost) as total'))->where('delivery_status', 'delivered');
        if ($intervalType == 'DAY') {
            $delivery_cost_query->whereDate('created_at', Carbon::today());
        }
        elseif($intervalType == 'WEEK' || $intervalType == 'MONTH') {
            $day = $intervalType == 'WEEK' ? 7 : 30;
            $delivery_cost_query->whereDate('created_at', '>', Carbon::now()->subDays($day));
        }
        $data['delivery'] = $delivery_cost_query->first()->total;

        // Product Sales
        $product_sale_query = Order::query();
        $product_sale_query = $product_sale_query->select(DB::raw('SUM(grand_total) as total'))->where('delivery_status', 'delivered');
        if ($intervalType == 'DAY') {
            $product_sale_query->whereDate('created_at', Carbon::today());
        }
        elseif($intervalType == 'WEEK' || $intervalType == 'MONTH') {
            $day = $intervalType == 'WEEK' ? 7 : 30;
            $product_sale_query->whereDate('created_at', '>', Carbon::now()->subDays($day));
        }
        $product_sale = $product_sale_query->orderBy(DB::raw('MONTH(created_at)'), 'asc')->first();
        $data['product_sale'] = $product_sale->total - ($data['commission'] + $data['delivery']);

        // Seller Subscription
        $data['seller_subscription'] = 0.00;
        if (addon_is_activated('seller_subscription')) {
            $seller_subscription = SellerPackagePayment::query();
            if ($intervalType == 'DAY') {
                $seller_subscription->whereDate('created_at', Carbon::today());
            }
            elseif($intervalType == 'WEEK' || $intervalType == 'MONTH') {
                $day = $intervalType == 'WEEK' ? 7 : 30;
                $seller_subscription->whereDate('created_at', '>', Carbon::now()->subDays($day));
            }
            $seller_subscription->select(DB::raw('SUM(amount) as total_amount'))->where('approval', 1);
            $data['seller_subscription'] = $seller_subscription->first()->total_amount;
        }

        // Customer Subscription
        $customer_subscription = CustomerPackagePayment::query();
        if ($intervalType == 'DAY') {
            $customer_subscription->whereDate('created_at', Carbon::today());
        }
        elseif($intervalType == 'WEEK' || $intervalType == 'MONTH') {
            $day = $intervalType == 'WEEK' ? 7 : 30;
            $customer_subscription->whereDate('created_at', '>', Carbon::now()->subDays($day));
        }
        $customer_subscription->select(DB::raw('SUM(amount) as total_amount'))->where('approval', 1);
        $data['customer_subscription'] = $customer_subscription->first()->total_amount;

        return $data;
    }

    // payouts
    public function payouts(Request $request)
    {
        $intervalType = $request->interval_type;
        // Seller payout
        $seller_payout = Payment::where('payment_method','!=','Seller paid to admin');
        if ($intervalType == 'DAY') {
            $seller_payout->whereDate('created_at', Carbon::today());
        }
        elseif($intervalType == 'WEEK' || $intervalType == 'MONTH') {
            $day = $intervalType == 'WEEK' ? 7 : 30;
            $seller_payout->whereDate('created_at', '>', Carbon::now()->subDays($day));
        }
        $seller_payout->select(DB::raw('SUM(amount) as total_payout'));
        $data['seller_payout'] = $seller_payout->first()->total_payout;

        // Refund Amount
        $refund_amount = 0;
        if (addon_is_activated('refund_request')) {
            $refund_request = RefundRequest::where('admin_approval', 1);
            if ($intervalType == 'DAY') {
                $refund_request->whereDate('created_at', Carbon::today());
            }
            elseif($intervalType == 'WEEK' || $intervalType == 'MONTH') {
                $day = $intervalType == 'WEEK' ? 7 : 30;
                $refund_request->whereDate('created_at', '>', Carbon::now()->subDays($day));
            }
            $refund_request->select(DB::raw('SUM(refund_amount) as total'));
            $refund_amount = $refund_request->first()->total;
        }
        $data['product_refund'] = $refund_amount;

        // Delivery Boy payout
        $delivery_boy_payout = 0;
        if (addon_is_activated('delivery_boy')) {

            $delivery_boy_payout = DeliveryBoyPayment::query();
            if ($intervalType == 'DAY') {
                $delivery_boy_payout->whereDate('created_at', Carbon::today());
            }
            elseif($intervalType == 'WEEK' || $intervalType == 'MONTH') {
                $day = $intervalType == 'WEEK' ? 7 : 30;
                $delivery_boy_payout->whereDate('created_at', '>', Carbon::now()->subDays($day));
            }
            $delivery_boy_payout->select(DB::raw('SUM(payment) as total'));
            $delivery_boy_payout = $delivery_boy_payout->first()->total;
        }

        $data['delivery_boy_payout'] = $delivery_boy_payout;
        return $data;
    }

    // Sale Analytic
    function sale_analytic(Request $request)
    {
        $intervalType = $request->interval_type;
        // product Sale Analytics
        $order_query = Order::where('delivery_status', 'delivered')->groupBy('time')->whereYear('created_at', Carbon::now()->year);
        if($intervalType == 'MONTH'){
            $order_query->select(DB::raw('SUM(grand_total) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'));
        }
        else{
            $order_query->select(DB::raw('SUM(grand_total) as total'), DB::raw('DATE_FORMAT(created_at, "%d") AS time'))
                ->whereMonth('created_at', Carbon::now()->month);
        }
        $orders = $order_query->orderBy(DB::raw('Date(created_at)'), 'asc')->get();
        // product Sale Analytics end

        // Earning from Seller Subscription
        $seller_subscriptions = array();
        if (addon_is_activated('seller_subscription')) {
            $seller_subscriptions_query = SellerPackagePayment::groupBy('time')->where('approval', 1)->whereYear('created_at', Carbon::now()->year);
            if($intervalType == 'MONTH'){
                $seller_subscriptions_query->select(DB::raw('SUM(amount) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'));
            }
            else{
                $seller_subscriptions_query->select(DB::raw('SUM(amount) as total'), DB::raw('DATE_FORMAT(created_at, "%d") AS time'))
                    ->whereMonth('created_at', Carbon::now()->month);
            }
            $seller_subscriptions = $seller_subscriptions_query->orderBy(DB::raw('Date(created_at)'), 'asc')->get();
        }
        // Earning from Seller Subscription End

        // Earning from Customer Subscription
        $customer_subscription_query = CustomerPackagePayment::groupBy('time')->where('approval', 1)->whereYear('created_at', Carbon::now()->year);
        if($intervalType == 'MONTH'){
            $customer_subscription_query->select(DB::raw('SUM(amount) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'));
        }
        else{
            $customer_subscription_query->select(DB::raw('SUM(amount) as total'), DB::raw('DATE_FORMAT(created_at, "%d") AS time'))
                ->whereMonth('created_at', Carbon::now()->month);
        }

        $customer_subscriptions = $customer_subscription_query->orderBy(DB::raw('Date(created_at)'), 'asc')->get();
        // Earning from Customer Subscription End

        $mymonths = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $new_data = array();
        if ($intervalType == 'MONTH') {
            foreach ($mymonths as $month) {
                $data['bg_color'] = "#1D82FA";
                $data['time'] = $month;
                $data['total'] = 0;
                foreach ($orders as $order) {
                    if ($order->time == $month) {
                        $data['total'] += $order->total;
                    }
                }
                foreach ($seller_subscriptions as $seller_subscription) {
                    if ($seller_subscription->time == $month) {
                        $data['total'] += $seller_subscription->total;
                    }
                }
                foreach ($customer_subscriptions as $customer_subscription) {
                    if ($customer_subscription->time == $month) {
                        $data['total'] += $customer_subscription->total;
                    }
                }

                $new_data[] = $data;
            }
        }
        else {
            $days = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
            for($i=1 ; $i<=$days; $i++) {
                $data['time'] = $i;
                $data['total'] = 0;
                $data['bg_color'] = '#1D82FA';

                foreach ($orders as $order) {
                    if ($order->time == $i) {
                        $data['total'] += $order->total;
                    }
                }
                foreach ($seller_subscriptions as $seller_subscription) {
                    if ($seller_subscription->time == $i) {
                        $data['total'] += $seller_subscription->total;
                    }
                }
                foreach ($customer_subscriptions as $customer_subscription) {
                    if ($customer_subscription->time == $i) {
                        $data['total'] += $customer_subscription->total;
                    }
                }
                if($intervalType == 'TODAY'){
                    $data['bg_color'] = $i == date('d') ? '#1D82FA' : '#D9D8D8';

                }
                elseif($intervalType == 'WEEK'){
                    $day = date('d');
                    $last7Days =  array();
                    for($j=1 ; $j<=7 ; $j++ )
                    {
                        if($day > 0){
                            array_push($last7Days, $day);
                            $day = $day-1;
                        }
                    }
                    $data['bg_color'] = in_array($i, $last7Days) ? '#1D82FA' :'#D9D8D8';
                }
                $new_data[] = $data;
            }
        }

        return response()->json($new_data);
    }

    // Payout Analytic
    function payout_analytic(Request $request)
    {
        $intervalType = $request->interval_type;
        // Seller payments
        $seller_payment_query = Payment::groupBy('time')->where('payment_method','!=','Seller paid to admin')->whereYear('created_at', Carbon::now()->year);
        if($intervalType == 'MONTH'){
            $seller_payment_query->select(DB::raw('SUM(amount) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'))
                        ->orderBy(DB::raw('MONTH(created_at)'), 'asc');
        }
        else {
            $seller_payment_query->select(DB::raw('SUM(amount) as total'), DB::raw('DATE_FORMAT(created_at, "%d") AS time'))
                ->whereMonth('created_at', Carbon::now()->month)
                ->orderBy(DB::raw('Date(created_at)'), 'asc');
        }
        $seller_payments = $seller_payment_query->get();

        // Refunds
        $refunds = array();
        if (addon_is_activated('refund_request')) {
            $refund_query = RefundRequest::groupBy('time')->where('admin_approval', 1)->whereYear('created_at', Carbon::now()->year);
            if($intervalType == 'MONTH'){
                $refund_query->select(DB::raw('SUM(refund_amount) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'))
                        ->orderBy(DB::raw('MONTH(created_at)'), 'asc');
            }
            else {
                $refund_query->select(DB::raw('SUM(refund_amount) as total'), DB::raw('DATE_FORMAT(created_at, "%d") AS time'))
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->orderBy(DB::raw('Date(created_at)'), 'asc');
            }
            $refunds = $refund_query->get();
        }

        // Delivery Boy Payments
        $delivery_boy_payments = array();
        if (addon_is_activated('delivery_boy')) {
            $delivery_boy_payment_query = DeliveryBoyPayment::groupBy('time')->whereYear('created_at', Carbon::now()->year);
            if($intervalType == 'MONTH'){
                $delivery_boy_payment_query->select(DB::raw('SUM(payment) as total'), DB::raw('DATE_FORMAT(created_at, "%M") AS time'))
                                    ->orderBy(DB::raw('MONTH(created_at)'), 'asc');
            }
            else {
                $delivery_boy_payment_query->select(DB::raw('SUM(payment) as total'), DB::raw('DATE_FORMAT(created_at, "%d") AS time'))
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->orderBy(DB::raw('Date(created_at)'), 'asc');
            }
            $delivery_boy_payments = $delivery_boy_payment_query->get();
        }

        $mymonths = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $new_data = array();
        if ($intervalType == 'MONTH') {
            foreach ($mymonths as $month) {
                $data['bg_color'] = "#1D82FA";
                $data['time'] = $month;
                $data['total'] = 0;
                foreach ($seller_payments as $seller_payment) {
                    if ($seller_payment->time == $month) {
                        $data['total'] += $seller_payment->total;
                    }
                }
                foreach ($refunds as $refund) {
                    if ($refund->time == $month) {
                        $data['total'] += $refund->total;
                    }
                }
                foreach ($delivery_boy_payments as $delivery_boy_payment) {
                    if ($delivery_boy_payment->time == $month) {
                        $data['total'] += $delivery_boy_payment->total;
                    }
                }
                $new_data[] = $data;
            }
        }
        else {
            $days = cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y'));
            for($i=1 ; $i<=$days; $i++) {
                $data['time'] = $i;
                $data['total'] = 0;
                $data['bg_color'] = '#1D82FA';

                foreach ($seller_payments as $seller_payment) {
                    if ($seller_payment->time == $i) {
                        $data['total'] += $seller_payment->total;
                    }
                }
                foreach ($refunds as $refund) {
                    if ($refund->time == $i) {
                        $data['total'] += $refund->total;
                    }
                }
                foreach ($delivery_boy_payments as $delivery_boy_payment) {
                    if ($delivery_boy_payment->time == $i) {
                        $data['total'] += $delivery_boy_payment->total;
                    }
                }
                if($intervalType == 'TODAY'){
                    $data['bg_color'] = $i == date('d') ? '#1D82FA' : '#D9D8D8';

                }
                elseif($intervalType == 'WEEK'){
                    $day = date('d');
                    $last7Days =  array();
                    for($j=1 ; $j<=7 ; $j++ )
                    {
                        if($day > 0){
                            array_push($last7Days, $day);
                            $day = $day-1;
                        }
                    }
                    $data['bg_color'] = in_array($i, $last7Days) ? '#1D82FA' :'#D9D8D8';
                }
                $new_data[] = $data;
            }
        }

        return response()->json($new_data);
    }

    public function packageAmountStoreIntoPackagePaymentTable()
    {
        $customerPackagePayments = CustomerPackagePayment::where('amount','<',1)->get();
        foreach($customerPackagePayments as $customerPackagePayment){
            $customerPackagePayment->amount = $customerPackagePayment->customer_package->amount;
            $customerPackagePayment->save();
        }

        if(addon_is_activated('seller_subscription')){
            $sellerPackagePayments = SellerPackagePayment::where('amount','<',1)->get();
            foreach($sellerPackagePayments as $sellerPackagePayment){
                $sellerPackagePayment->amount = $sellerPackagePayment->seller_package->amount;
                $sellerPackagePayment->save();
            }
        }
    }
}
