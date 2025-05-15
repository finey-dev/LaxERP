<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class CreateRawMaterial
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $raw_material;

    public function __construct($request, $raw_material)
    {
        $this->request = $request;
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
