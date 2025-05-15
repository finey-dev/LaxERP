<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRequestCategory
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $requestscategory;

    public function __construct($requestscategory)
    {
        $this->requestscategory = $requestscategory;
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
