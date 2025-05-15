<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class CreateQualityStandard
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $quality_standards;

    public function __construct($request, $quality_standards)
    {
        $this->request = $request;
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
