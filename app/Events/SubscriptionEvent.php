<?php

namespace App\Events;

use App\Models\User;
use App\Models\UsersRelation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SubscriptionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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

    /* Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        if($this->user->id == $this->subscription->following->id) {
            return [
                new PrivateChannel('subscription' . ".user." .  $this->subscription->follower->id)
            ];
        }
        return [
            new PrivateChannel('subscription' . ".user." .  $this->subscription->following->id) 
        ];
    }

    public function broadcastAs()
    {
        return 'subscription';
    }
}
