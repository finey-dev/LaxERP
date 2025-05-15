<?php

namespace Workdo\SWOTAnalysisModel\Events;

use Illuminate\Queue\SerializesModels;

class CreateSWOTAnalysismodel
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $request;
    public $swotanalysismodel;

    public function __construct($request, $swotanalysismodel)
    {
        $this->request = $request;
        $this->swotanalysismodel = $swotanalysismodel;
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
