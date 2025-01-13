<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessStatusCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $userId;
    public $name;

    public function __construct( $userId, $name)
    {
        $this->userId = $userId;
        $this->name = $name;
    }

    public function broadcastOn()
    {
        return new Channel("processes.{$this->userId}");
    }
}
