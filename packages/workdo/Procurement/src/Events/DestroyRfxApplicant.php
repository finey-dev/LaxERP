<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRfxApplicant
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $rfx_applicant;

    public function __construct($rfx_applicant)
    {
        $this->rfx_applicant = $rfx_applicant;
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
