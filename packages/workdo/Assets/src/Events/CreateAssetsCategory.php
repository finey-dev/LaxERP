<?php

namespace Workdo\Assets\Events;

use Illuminate\Queue\SerializesModels;

class CreateAssetsCategory
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $category;

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
