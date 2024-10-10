<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\UsersRelation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserSubscribed extends Notification
{
    use Queueable;

    public $subscription;

    public $message;

    public User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(UsersRelation $subscription, User $user)
    {
        $this->subscription = $subscription;
        $this->user = $user;
        $this->message = $this->subscription->status == "pending" ?
            $this->subscription->follower->username . " a demandé à vous suivre !" :
            $this->subscription->following->username  . " a accepté votre demande d'invitation !";
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'subscription_id' => $this->subscription->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        if($this->user->id == $this->subscription->following->id) {
            return new BroadcastMessage([
                'message' => $this->message,
                'subscription' =>  $this->subscription->following->id
            ]);
        }
        return new BroadcastMessage([
            'message' => $this->message,
            'subscription' =>  $this->subscription->follower->id
        ]);
    }
}
