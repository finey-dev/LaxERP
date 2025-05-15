<?php

namespace Workdo\MachineRepairManagement\Events;

use Illuminate\Queue\SerializesModels;

class ProductDestroyDiagnosis
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $invoiceProduct;

    public function __construct($request,$invoiceProduct)
    {
        $this->request = $request;
        $this->invoiceProduct = $invoiceProduct;
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
