<?php

namespace Workdo\Quotation\Events;

use Illuminate\Queue\SerializesModels;

class UpdateQuotation
{
    use SerializesModels;

    public $request;
    public $quotation;

    public function __construct($request ,$quotation)
    {
        $this->request = $request;
        $this->quotation = $quotation;
    }

}
