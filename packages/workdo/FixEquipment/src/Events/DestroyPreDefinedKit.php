<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyPreDefinedKit
{
    use SerializesModels;

    public $kit;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($kit)
    {
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
