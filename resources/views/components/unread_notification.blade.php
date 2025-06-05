@props(['notifications'])

@php
    $notificationShowDesign = get_setting('notification_show_type');
    if($notificationShowDesign != 'only_text'){
        $notifyImageDesign = '';
        if($notificationShowDesign == 'design_2'){
            $notifyImageDesign = 'rounded-1';
        }
        elseif($notificationShowDesign == 'design_3'){
            $notifyImageDesign = 'rounded-circle';
        }
    }
@endphp
@forelse($notifications as $notification)
    <li class="list-group-item d-flex justify-content-between align-items- py-3">
        <div class="media text-inherit">
            <div class="media-body">
                @php
                    $user_type = auth()->user()->user_type;
                    $notificationType = get_notification_type($notification->notification_type_id, 'id');
                    $notifyContent = $notificationType->getTranslation('default_text');
                @endphp
                <div class="d-flex">
                    @if($notificationShowDesign != 'only_text')
                        <div class="size-35px mr-2">
                            <img
                                src="{{ uploaded_asset($notificationType->image) }}"
                                onerror="this.onerror=null;this.src='{{ static_asset('assets/img/notification.png') }}';"
                                class="img-fit h-100 {{ $notifyImageDesign }}" >
                        </div>
                    @endif
                    <div>
                        {{-- Order Related Notifications --}}
                        @if ($notification->type == 'App\Notifications\OrderNotification')
                        @php
                            $orderCode  = $notification->data['order_code'];
                            $orderCode = "<span class='text-blue'>".$orderCode."</span>";
                            $notifyContent = str_replace('[[order_code]]', $orderCode, $notifyContent);
                        @endphp

                        {{-- Shop Verification Related Notifications --}}
                        @elseif ($notification->type == 'App\Notifications\ShopVerificationNotification')
                            @php
                                if($notification->data['status'] == 'submitted'){
                                    $shopName = "<span class='text-blue'>".$notification->data['name']."</span>";
                                    $notifyContent = str_replace('[[shop_name]]', $shopName, $notifyContent);
                                }
                            @endphp

                        {{-- Shop Product Related Notifications --}}
                        @elseif ($notification->type == 'App\Notifications\ShopProductNotification')
                            @php
                                $product_id     = $notification->data['id'];
                                $product_type   = $notification->data['type'];
                                $lang           = env('DEFAULT_LANGUAGE');
                                $productName = "<span class='text-blue'>".$notification->data['name']."</span>";
                                $notifyContent = str_replace('[[product_name]]', $productName, $notifyContent);
                            @endphp

                        {{-- Seller Payout Notifications --}}
                        @elseif ($notification->type == 'App\Notifications\PayoutNotification')
                            @php
                                $amount = single_price($notification->data['payment_amount']);
                                $shopName = "<span class='text-blue'>".$notification->data['name']."</span>";

                                $notifyContent = str_replace('[[shop_name]]', $shopName, $notifyContent);
                                $notifyContent = str_replace('[[amount]]', $amount, $notifyContent);
                            @endphp
                        @endif
                        <a href="{{ ($user_type == 'admin' || $user_type == 'staff') ?
                                    route('admin.notification.read-and-redirect', encrypt($notification->id)) :
                                    route('seller.notification.read-and-redirect', encrypt($notification->id)) }}">
                            <p class="mb-1 text-dark text-truncate-2">
                                {!! $notifyContent !!}
                            </p>
                            <small class="text-muted">
                                {{ date('F j Y, g:i a', strtotime($notification->created_at)) }}
                            </small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </li>
@empty
    <li class="list-group-item">
        <div class="py-4 text-center fs-16">
            {{ translate('No notification found') }}
        </div>
    </li>
@endforelse
