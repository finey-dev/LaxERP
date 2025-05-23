<?php

namespace Workdo\Hrm\Events;

use Illuminate\Queue\SerializesModels;

class DestroySaturationDeduction
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $saturationdeduction;

    public function __construct($saturationdeduction)
    {
        $this->saturationdeduction = $saturationdeduction;
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
