<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApproveFoodRequestNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $food;
    private $order;
    public function __construct($food,$order)
    {
        $this->food = $food;
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
        $food = $this->food;
        $order = $this->order;
        return (new MailMessage)
                ->from('support@webhunter24.com')->view('email.notification.approve-food-request',compact('food','order'))
                ->subject('Approve Food Request Notification Mail');
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
            'data' => 'Approve Food Request Notification',
            'order_id' => $order['id'], 
            'food_id' => $order['food_id'],
        ];
    }
}
