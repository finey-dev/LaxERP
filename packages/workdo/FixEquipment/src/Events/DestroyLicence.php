<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyLicence
{
    use SerializesModels;

    public $license;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($license)
    {
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
