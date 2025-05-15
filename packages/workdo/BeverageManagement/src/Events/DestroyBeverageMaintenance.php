<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyBeverageMaintenance
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $beverage_maintenance;

    public function __construct($beverage_maintenance)
    {
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
