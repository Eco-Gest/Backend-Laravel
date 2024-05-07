<?php

namespace App\Events;

use App\Models\User;
use App\Models\Subscription;
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

    /**
     * Create a new notification instance.
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
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
        return [
            $this->subscription->status == "pending" ? 
            new PrivateChannel('subscription' . ".user." .  $this->subscription->following->id) : 
            new PrivateChannel('subscription' . ".user." .  $this->subscription->follower->id) 
        ];
    }

    public function broadcastAs()
    {
        return 'subscription';
    }
}
