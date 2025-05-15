<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class UpdateQualityCheck
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $quality_checks;

    public function __construct($request, $quality_checks)
    {
        $this->request = $request;
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
