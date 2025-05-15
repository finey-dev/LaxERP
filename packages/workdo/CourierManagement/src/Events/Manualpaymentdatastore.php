<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Manualpaymentdatastore
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $courierPayment;
    public $receiverDetails;
    public $request;
    public function __construct($courierPayment, $receiverDetails, $request)
    {
        $this->courierPayment = $courierPayment;
        $this->receiverDetails = $receiverDetails;
        $this->request = $request;
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
