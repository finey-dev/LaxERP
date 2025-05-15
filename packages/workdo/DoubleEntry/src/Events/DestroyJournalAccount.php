<?php

namespace Workdo\DoubleEntry\Events;

use Illuminate\Queue\SerializesModels;

class DestroyJournalAccount
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $journalEntry;

    public function __construct($journalEntry)
    {
        $this->journalEntry = $journalEntry;
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
