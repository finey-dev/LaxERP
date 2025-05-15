<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class CreateLicence
{
    use SerializesModels;

    public $request;

    public $license;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $license)
    {
        $this->request = $request;
        $this->license = $license;
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
