<?php

namespace App\Models;

use App\Traits\PreventDemoModeChanges;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersExport implements FromCollection, WithMapping, WithHeadings
{
    use PreventDemoModeChanges;

    protected $order_ids;

    public function __construct($order_ids)
    {
        $this->order_ids = $order_ids;
    }

    public function collection()
    {
        return Order::findMany($this->order_ids);
    }

    public function headings(): array
    {
        return [
            'Order Code',
            'Num. of Products',
            'Customer',
            'Seller',
            'Amount',
            'Delivery Status',
            'Payment method',
            'Payment Status',
        ];
    }

    /**
    * @var Order  $order
    */
    public function map($order): array
    {
        return [
            $order->code,
            count($order->orderDetails),
            $order->user != null ? $order->user->name : '',
            $order->shop != null ? $order->shop->name : translate('Inhouse Order'),
            single_price($order->grand_total),
            translate(ucfirst(str_replace('_', ' ', $order->delivery_status))),
            translate(ucfirst(str_replace('_', ' ', $order->payment_type))),
            translate(ucfirst($order->payment_status)),
        ];
    }
}