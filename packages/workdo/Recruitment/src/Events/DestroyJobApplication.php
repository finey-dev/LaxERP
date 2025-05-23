<?php

namespace Workdo\Recruitment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyJobApplication
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $jobApplication;

    public function __construct($jobApplication)
    {
        $this->jobApplication = $jobApplication;
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
