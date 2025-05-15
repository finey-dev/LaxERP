<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyBillItemMaterial
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $bill_of_material;

    public function __construct($bill_of_material)
    {
        $this->bill_of_material = $bill_of_material;
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
