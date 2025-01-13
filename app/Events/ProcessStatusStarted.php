<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ProcessStatusStarted implements ShouldBroadcastNow
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
