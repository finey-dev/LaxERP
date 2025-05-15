<?php

namespace Workdo\Planning\Events;

use Illuminate\Queue\SerializesModels;

class UpdateChallenge
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $Challenges;

    public function __construct($request, $Challenges)
    {
        $this->request = $request;
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
