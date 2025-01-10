<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class TranscriptionCompleted implements ShouldBroadcastNow
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
