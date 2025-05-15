<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class CreateBudgetType
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $rfx;

    public function __construct($request, $budgettype)
    {
        $this->request = $request;
        $this->rfx = $budgettype;
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
