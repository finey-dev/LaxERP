<?php

namespace Workdo\CourierManagement\Events;

use Illuminate\Queue\SerializesModels;

class ServiceAgreementsdelete
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $service_agreements;
    public $request;
    public function __construct($service_agreements, $request)
    {
        $this->service_agreements = $service_agreements;
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
