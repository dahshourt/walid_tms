<?php

namespace App\Events;

use App\Models\Defect;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DefectCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Defect $defect;
    public int $groupId;
    public int $statusId;

    public bool $afterCommit = true;

    public function __construct(Defect $defect, int $groupId, int $statusId)
    {
        $this->defect = $defect;
        $this->groupId = $groupId;
        $this->statusId = $statusId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
