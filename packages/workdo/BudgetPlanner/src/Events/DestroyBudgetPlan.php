<?php

namespace Workdo\BudgetPlanner\Events;

use Illuminate\Queue\SerializesModels;

class DestroyBudgetPlan
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $budgetplan;

    public function __construct($budgetplan)
    {
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
