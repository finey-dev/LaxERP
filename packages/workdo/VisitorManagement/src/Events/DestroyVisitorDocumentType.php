<?php

namespace Workdo\VisitorManagement\Events;

use Illuminate\Queue\SerializesModels;

class DestroyVisitorDocumentType
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $document_type;
    public function __construct($document_type)
    {
        $this->document_type = $document_type;
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
