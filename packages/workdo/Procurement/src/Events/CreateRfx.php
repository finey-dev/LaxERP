<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class CreateRfx
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $rfx;

    public function __construct($request, $rfx)
    {
        $this->request = $request;
        $this->rfx = $rfx;
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
