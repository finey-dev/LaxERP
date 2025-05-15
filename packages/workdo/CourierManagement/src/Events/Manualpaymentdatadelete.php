<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Manualpaymentdatadelete
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $courierPaymentDetails;
    public $request;
    public function __construct($courierPaymentDetails, $request)
    {
        $this->courierPaymentDetails = $courierPaymentDetails;
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
