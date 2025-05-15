<?php

namespace Workdo\BeverageManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyCollectionCenter
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $collection_center;

    public function __construct($collection_center)
    {
        $this->collection_center = $collection_center;
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
