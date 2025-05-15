<?php

namespace Workdo\FileSharing\Events;

use Illuminate\Queue\SerializesModels;

class FileVerificationDelete
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $verification;
    public function __construct($verification)
    {
        $this->verification = $verification;
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
