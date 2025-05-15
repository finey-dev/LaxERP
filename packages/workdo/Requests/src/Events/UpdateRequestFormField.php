<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class UpdateRequestFormField
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $Requests;

    public function __construct($request,$Requests)
    {
        $this->request = $request;
        $this->Requests = $Requests;
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
