<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyRfxCustomQuestion
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $question;
    public function __construct($question)
    {
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
