<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class UpdateRequestSubCategory
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $requestsubscategory;

    public function __construct($request,$requestsubscategory)
    {
        $this->request = $request;
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
