<?php

namespace Workdo\FixEquipment\Events;

use Illuminate\Queue\SerializesModels;

class CreateAsset
{
    use SerializesModels;

    public $request;

    public $asset;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($request, $asset)
    {
        $this->request = $request;
        $this->asset = $asset;
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
