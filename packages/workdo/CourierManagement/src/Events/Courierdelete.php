<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Courierdelete
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $receiverData;
    public $packageData;
    public $packagePaymentData;
    public $courierTrackingStatus;
    public function __construct($receiverData, $packageData, $packagePaymentData,$courierTrackingStatus)
    {
        $this->receiverData = $receiverData;
        $this->packageData = $packageData;
        $this->packagePaymentData = $packagePaymentData;
        $this->courierTrackingStatus = $courierTrackingStatus;
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
