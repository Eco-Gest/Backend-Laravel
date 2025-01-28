<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Pusher\PushNotifications\PushNotifications;

class PostCommented extends Notification
{
    use Queueable;
    public $message;
    public User $user;

    public Post $post;

    public Comment $comment;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Post $post, Comment $comment)
    {
        $this->user = $user;
        $this->post = $post;
        $this->comment = $comment;
        $this->message = $this->user->username . ' a commenté votre publication !';
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
            'comment_id' => $this->comment->id,
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
        return new PrivateChannel('comment.user.' . $this->post->user->id);
    }

    /**
     * Envoi une notification push via Pusher Beams.
     */
    private function sendPushNotification($notifiable)
    {
        // Initialise Pusher Beams
        $beamsClient = new PushNotifications([
            'instanceId' => env('PUSHER_BEAMS_INSTANCE_ID'),
            'secretKey' => env('PUSHER_BEAMS_SECRET_KEY'),
        ]);

        // Récupère les informations nécessaires
        $interest = 'user-' . $this->post->user->id; // Crée un intérêt unique par utilisateur
        $title = 'Nouveau commentaire';
        $body = $this->message;

        // Envoie la notification
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

