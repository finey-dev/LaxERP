<?php

namespace Workdo\SupportTicket\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTicket extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('support-ticket::email.create_ticket')->subject('Ticket Opened ['.$this->ticket->ticket_id.']');

    }
}

