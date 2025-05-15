<?php

namespace Workdo\Planning\Events;

use Illuminate\Queue\SerializesModels;

class DestroyPlanningCategories
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    public $PlanningCategories;

    public function __construct($PlanningCategories)
    {
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
