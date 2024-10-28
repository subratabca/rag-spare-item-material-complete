<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FoodRequestNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $order = $this->order;
        return (new MailMessage)
                ->from('support@webhunter24.com')->view('email.notification.food-request',compact('order'))
                ->subject('Food Request Notification Mail');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $order = $this->order;
        return [
            'data' => 'Food Request Notification',
            'order_id' => $order['id'], 
            'food_id' => $order['food_id'],
        ];
    }
}
