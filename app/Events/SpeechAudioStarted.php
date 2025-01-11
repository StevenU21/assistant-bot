<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpeechAudioStarted implements ShouldBroadcastNow
{
    use SerializesModels;
    public $userId;

    public function __construct( $userId)
    {
        $this->userId = $userId;

    }

    public function broadcastOn()
    {
        return new Channel("transcriptions.{$this->userId}");
    }
}
