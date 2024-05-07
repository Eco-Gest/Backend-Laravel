<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $notifications = [];
        foreach ($this->post->userPostParticipation as $participant) {
            $notifications[] = new PrivateChannel('comment' . ".user." . $participant->participant_id);
        }
        return $notifications;
    }

    public function broadcastAs()
    {
        return 'comment';
    }
}
