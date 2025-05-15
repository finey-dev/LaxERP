<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class Couriercreate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $receiverDetails;
    public $courierPackageInfo;
    public $request;
    public function __construct($receiverDetails,$courierPackageInfo,$request)
    {
        $this->receiverDetails = $receiverDetails;
        $this->courierPackageInfo = $courierPackageInfo;
        $this->request= $request;
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
