<?php

namespace Workdo\Quotation\Events;

use Illuminate\Queue\SerializesModels;

class DestroyQuotation
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $quotation;

    public function __construct($quotation)
    {
        $this->quotation = $quotation;
    }

}
