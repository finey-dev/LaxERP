<?php

namespace Workdo\Sales\Events;

use Illuminate\Queue\SerializesModels;

class CreateSalesInvoice
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $invoice;

    public function __construct($request,$invoice)
    {
        $this->request = $request;
        $this->invoice = $invoice;
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
