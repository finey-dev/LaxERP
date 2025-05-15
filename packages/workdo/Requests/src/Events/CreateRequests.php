<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class CreateRequests
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $CreateRequests;

    public function __construct($request,$CreateRequests)
    {
        $this->request = $request;
        $this->CreateRequests = $CreateRequests;
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
