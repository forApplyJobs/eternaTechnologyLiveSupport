<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewIceCandidate implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $iceCandidate;

    /**
     * Create a new event instance.
     *
     * @param  array  $iceCandidate
     */
    public function __construct($iceCandidate)
    {
        $this->iceCandidate = $iceCandidate;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('iceCandidates.' . $this->iceCandidate['receiver_id']),
        ];
    }
    public function broadcastAs()
    {
        return 'newIceCandidate';
    }
    public function broadcastWith(){
        return ["iceCandidate"=>$this->iceCandidate];
    }
}
