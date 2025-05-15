<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class UpdateRFxCategory
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $rfxCategory;
    public function __construct($request, $rfxCategory)
    {
        $this->request = $request;
        $this->rfxCategory = $rfxCategory;
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
