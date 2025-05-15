<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyQualityStandard
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $quality_standards;

    public function __construct($quality_standards)
    {
        $this->quality_standards = $quality_standards;
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
