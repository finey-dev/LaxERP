<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyPackaging
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $packaging;

    public function __construct($packaging)
    {
        $this->packaging = $packaging;
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
