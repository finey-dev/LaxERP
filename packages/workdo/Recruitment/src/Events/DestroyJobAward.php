<?php

namespace Workdo\Recruitment\Events;

use Illuminate\Queue\SerializesModels;

class DestroyJobAward
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $job_award;

    public function __construct($job_award)
    {
        $this->job_award = $job_award;
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
