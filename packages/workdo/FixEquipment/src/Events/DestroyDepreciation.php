<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyDepreciation
{
    use SerializesModels;

    public $depreciation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($depreciation)
    {
        $this->depreciation = $depreciation;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
