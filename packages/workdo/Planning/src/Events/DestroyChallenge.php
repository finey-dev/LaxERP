<?php

namespace Workdo\Planning\Events;

use Illuminate\Queue\SerializesModels;

class DestroyChallenge
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $Challenges;

    public function __construct($Challenges)
    {
        $this->Challenges = $Challenges;
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
