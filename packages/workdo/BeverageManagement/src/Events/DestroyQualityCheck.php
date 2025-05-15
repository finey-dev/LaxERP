<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyQualityCheck
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $quality_checks;

    public function __construct($quality_checks)
    {
        $this->quality_checks = $quality_checks;
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
