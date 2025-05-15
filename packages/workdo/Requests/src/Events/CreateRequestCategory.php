<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class CreateRequestCategory
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $requestscategory;

    public function __construct($request,$requestscategory)
    {
        $this->request = $request;
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
