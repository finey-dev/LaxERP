<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class CreateRfxStage
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $rfxStage;
    public function __construct($request, $rfxStage)
    {
        $this->request = $request;
        $this->rfxStage = $rfxStage;
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
