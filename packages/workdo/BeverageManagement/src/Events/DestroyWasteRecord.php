<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyWasteRecord
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $waste_records;

    public function __construct($waste_records)
    {
        $this->waste_records = $waste_records;
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
