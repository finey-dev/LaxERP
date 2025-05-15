<?php

namespace Workdo\Requests\Events;

use Illuminate\Queue\SerializesModels;

class CreateRequestSubCategory
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $CreateSubCategory;

    public function __construct($request,$CreateSubCategory)
    {
        $this->request = $request;
        $this->CreateSubCategory = $CreateSubCategory;
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
