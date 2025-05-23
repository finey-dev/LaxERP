<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class UpdateCategory
{
    use SerializesModels;

    public $request;

    public $category;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $category)
    {
        $this->request = $request;
        $this->category = $category;
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
