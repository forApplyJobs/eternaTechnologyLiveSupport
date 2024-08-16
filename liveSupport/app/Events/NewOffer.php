<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOffer implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $offer;

    /**
     * Create a new event instance.
     *
     */
    public function __construct($offer)
    {
        $this->offer = $offer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // dd($this->offer['receiver_id']);
        return [
            new PrivateChannel('offer.' . $this->offer['receiver_id']),
        ];
    }
    public function broadcastAs()
    {
        return 'newOffer';
    }
    public function broadcastWith(){
        // dd(new PrivateChannel('offer.' . $this->offer['receiver_id']),);
        return ["offer"=>$this->offer];
    }
}
