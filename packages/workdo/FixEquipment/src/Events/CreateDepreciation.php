<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class CreateDepreciation
{
    use SerializesModels;

    public $request;

    public $depreciation;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $depreciation)
    {
        $this->request = $request;
        $this->depreciation = $depreciation;
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
