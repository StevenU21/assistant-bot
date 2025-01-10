<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class TranscriptionCompleted implements ShouldBroadcastNow
{
    use SerializesModels;

    public $transcriptionId;
    public $userId;

    public function __construct($transcriptionId, $userId)
    {
        $this->transcriptionId = $transcriptionId;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new Channel("transcriptions.{$this->userId}");
    }
}
