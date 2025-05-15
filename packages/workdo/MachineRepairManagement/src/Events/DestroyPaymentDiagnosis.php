<?php

namespace Workdo\MachineRepairManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyPaymentDiagnosis
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $invoice;
    public $payment;

    public function __construct($invoice,$payment)
    {
        $this->invoice = $invoice;
        $this->payment = $payment;
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
