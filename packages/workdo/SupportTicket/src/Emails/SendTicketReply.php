<?php

namespace Workdo\SupportTicket\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTicketReply extends Mailable
{
    use Queueable, SerializesModels;
    public $ticket;
    public $conversion;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($ticket,$conversion)
    {
        $this->ticket = $ticket;
        $this->conversion = $conversion;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('support-ticket::email.create_ticket_admin_reply')->subject('New reply on ticket '.$this->ticket->ticket_id);
    }
}
