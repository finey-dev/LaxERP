<?php

namespace Workdo\Planning\Events;

use Illuminate\Queue\SerializesModels;

class UpdatePlanningCategories
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $PlanningCategories;

    public function __construct($request, $PlanningCategories)
    {
        $this->request = $request;
        $this->PlanningCategories = $PlanningCategories;
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
