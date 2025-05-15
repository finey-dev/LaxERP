<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class UpdatePreDefinedKit
{
    use SerializesModels;

    public $request;

    public $kit;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $kit)
    {
        $this->request = $request;
        $this->kit = $kit;
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
