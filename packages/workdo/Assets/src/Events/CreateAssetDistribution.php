<?php

namespace Workdo\Assets\Events;

use Illuminate\Queue\SerializesModels;

class CreateAssetDistribution
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $assetdistribution;
    public $asset;

    public function __construct($request ,$assetdistribution , $asset)
    {
        $this->request = $request;
        $this->assetdistribution = $assetdistribution;
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
