<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class UpdateRfxCustomQuestion
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $question;
    public function __construct($request, $question)
    {
        $this->request = $request;
        $this->question = $question;
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
