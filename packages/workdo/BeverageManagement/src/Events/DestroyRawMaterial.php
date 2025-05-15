<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRawMaterial
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $raw_material;

    public function __construct($raw_material)
    {
        $this->raw_material = $raw_material;
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
