<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Manualpaymentdataupdate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $paymentDetails;
    public $courierInfo;
    public $request;
    public function __construct($paymentDetails, $courierInfo, $request)
    {
       $this->paymentDetails = $paymentDetails;
       $this->courierInfo = $courierInfo;
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
