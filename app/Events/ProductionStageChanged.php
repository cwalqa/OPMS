<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductionStageChanged
{
    use Dispatchable, SerializesModels;

    public $itemId;
    public $stageId;
    public $previousStageId;
    public $orderId;
    public $completedBy;

    /**
     * Create a new event instance.
     *
     * @param int $itemId
     * @param int $stageId
     * @param int|null $previousStageId
     * @param int $orderId
     * @param string|null $completedBy
     * @return void
     */
    public function __construct($itemId, $stageId, $previousStageId, $orderId, $completedBy)
    {
        $this->itemId = $itemId;
        $this->stageId = $stageId;
        $this->previousStageId = $previousStageId;
        $this->orderId = $orderId;
        $this->completedBy = $completedBy;
    }
}