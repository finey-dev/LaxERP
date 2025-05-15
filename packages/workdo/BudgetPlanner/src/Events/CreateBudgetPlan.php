<?php

namespace Workdo\BudgetPlanner\Events;

use Illuminate\Queue\SerializesModels;

class CreateBudgetPlan
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $budgetplan;

    public function __construct($request,$budgetplan)
    {
        $this->request = $request;
        $this->budgetplan = $budgetplan;
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
