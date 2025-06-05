<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderNotification extends Notification
{
    use Queueable;
    
    public $data;
    public $className;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order_notification)
    {
        $this->data = $order_notification;
        $this->className= OrderNotification::class;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DbNotification::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'notification_type_id' => $this->data['notification_type_id'],
            'data' => [
                'order_id'      => $this->data['order_id'],
                'order_code'    => $this->data['order_code'],
                'user_id'       => $this->data['user_id'],
                'seller_id'     => $this->data['seller_id'],
                'status'        => $this->data['status']
            ]
        ];
    }
}
