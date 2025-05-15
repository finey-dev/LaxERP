<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class CreateComponent
{
    use SerializesModels;

    public $request;

    public $component;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $component)
    {
        $this->request = $request;
        $this->component = $component;
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
