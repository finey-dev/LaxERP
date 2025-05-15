<?php

namespace Workdo\FileSharing\Events;

use Illuminate\Queue\SerializesModels;

class FileVerificationCreate
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $verification;
    public function __construct($request, $verification)
    {
        $this->request = $request;
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
