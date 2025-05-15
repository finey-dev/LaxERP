<?php

namespace Workdo\Procurement\Events;

use Illuminate\Queue\SerializesModels;

class RfxApplicationArchive
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

     public $UpdateRfxBoard;
    public function __construct($UpdateRfxBoard)
    {
        $this->UpdateRfxBoard = $UpdateRfxBoard;
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
