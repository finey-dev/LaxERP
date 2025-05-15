<?php

namespace Workdo\RepairManagementSystem\Events;

use Illuminate\Queue\SerializesModels;

class CretaeRepairInvoice
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $repair_invoice;
    
    public function __construct($request,$repair_invoice)
    {
        $this->request = $request;
        $this->repair_invoice = $repair_invoice;
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
