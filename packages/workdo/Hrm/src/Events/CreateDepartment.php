<?php

namespace Workdo\Hrm\Events;

use Illuminate\Queue\SerializesModels;

class CreateDepartment
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $request;
    public $department;

    public function __construct($request, $department)
    {
        $this->request = $request;
        $this->department = $department;
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
