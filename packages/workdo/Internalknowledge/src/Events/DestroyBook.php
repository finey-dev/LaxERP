<?php

namespace Workdo\Internalknowledge\Events;

use Illuminate\Queue\SerializesModels;

class DestroyBook
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $book;

    public function __construct($book)
    {
        $this->book = $book;
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
