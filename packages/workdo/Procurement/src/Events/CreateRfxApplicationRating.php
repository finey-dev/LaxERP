<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class CreateRfxApplicationRating
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $rfxApplication;

    public function __construct($request, $rfxApplication)
    {
        $this->request = $request;
        $this->rfxApplication = $rfxApplication;
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
