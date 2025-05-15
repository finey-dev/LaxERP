<?php

namespace Workdo\Internalknowledge\Events;

use Illuminate\Queue\SerializesModels;

class UpdateBook
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $book;

    public function __construct($request, $book)
    {
        $this->request = $request;
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
