<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCall implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $call;

    /**
     * Create a new event instance.
     */
    public function __construct($call)
    {
        $this->call=$call;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        
        return [
            new PrivateChannel('calls.'.$this->call['receiver_id']),
        ];
    }
    
    public function broadcastWith(){
        return ["call"=>$this->call];
    }
    public function broadcastAs(): string
    { 
        return 'call';
    }
}
