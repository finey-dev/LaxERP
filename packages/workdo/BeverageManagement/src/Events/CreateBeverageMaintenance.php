<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class CreateBeverageMaintenance
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $beverage_maintenance;

    public function __construct($request, $beverage_maintenance)
    {
        $this->request = $request;
        $this->beverage_maintenance = $beverage_maintenance;
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
