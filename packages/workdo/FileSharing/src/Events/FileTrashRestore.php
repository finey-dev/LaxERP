<?php

namespace Workdo\FileSharing\Events;

use Illuminate\Queue\SerializesModels;

class FileTrashRestore
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $trashFile;

    public function __construct($trashFile)
    {
        $this->trashFile = $trashFile;
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
