<?php

namespace Workdo\Recruitment\Events;

use Illuminate\Queue\SerializesModels;

class ConvertToEmployee
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $request;
    public $employee;

    public function __construct($request, $employee)
    {
        $this->request = $request;
        $this->employee = $employee;
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
