<?php

namespace Workdo\TeamWorkload\Events;

use Illuminate\Queue\SerializesModels;

class CreateWorloadHolidays
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $holiday;

    public function __construct($request ,$holiday)
    {
        $this->request = $request;
        $this->holiday = $holiday;
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
