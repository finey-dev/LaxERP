<?php

namespace Workdo\BusinessProcessMapping\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ComposeMail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $message;
    public $business;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $message, $business)
    {
        $this->subject = $subject;
        $this->message = $message;
        $this->business = $business;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(company_setting('mail_from_address'))
            ->subject($this->subject)
            ->markdown('business-process-mapping::businessprocessmapping.emailcontent')
            ->with([
                'message' => $this->message,
                'url' => $this->business,
            ]);
    }
}