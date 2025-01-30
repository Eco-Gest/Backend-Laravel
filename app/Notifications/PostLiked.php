<?php

namespace App\Notifications;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Pusher\PushNotifications\PushNotifications;

class PostLiked extends Notification
{
    use Queueable;

    public $like;
    public $message;
    public User $user;

    public Post $post;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Post $post, Like $like)
    {
        $this->user = $user;
        $this->post = $post;
        $this->like = $like;
        $this->message = $this->user->username . ' a liké votre publication !';
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
            'like_id' => $this->like->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
      $this->sendPushNotification($notifiable);

      return new BroadcastMessage([
                'message' => $this->message,
                'post_id' => $this->post->id
            ]);
    }

    public function broadcastOn()
    {
        return new PrivateChannel('like.user.' . $this->post->user->id);
    }

        /**
     * Send push notification with Pusher Beams.
     */
    private function sendPushNotification($notifiable)
    {
        $beamsClient = new PushNotifications([
            'instanceId' => env('PUSHER_BEAMS_INSTANCE_ID'),
            'secretKey' => env('PUSHER_BEAMS_SECRET_KEY'),
        ]);

        $interest = 'user-' . $this->post->user->id; 
        $title = 'Nouveau like';
        $body = $this->message;

        $beamsClient->publishToInterests(
            [$interest],
            [
                'web' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ],
                'fcm' => [
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                ],
                'apns' => [
                    'aps' => [
                        'alert' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                    ],
                ],
            ]
        );
    }
}