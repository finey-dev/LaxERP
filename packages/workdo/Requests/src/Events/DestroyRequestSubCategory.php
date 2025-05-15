<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRequestSubCategory
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $requestsubscategory;

    public function __construct($requestsubscategory)
    {
        $this->requestsubscategory = $requestsubscategory;
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
