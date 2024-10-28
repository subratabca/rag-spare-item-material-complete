<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FoodPublishNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private $food;

    public function __construct($food)
    {
        $this->food = $food;
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
        return (new MailMessage)
                ->from('support@webhunter24.com')->view('email.notification.food-publish',compact('food'))
                ->subject('Food Publish Notification Mail');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $food = $this->food;
        return [
            'data' => 'Food Publish Notification',
            'food_id' => $food['id'],
        ];
    }
}
