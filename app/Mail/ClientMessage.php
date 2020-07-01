<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientMessage extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var \stdClass $msg
     */
    protected $msg;

    /**
     * Create a new message instance.
     *
     * @param \stdClass $msg
     * @return void
     */
    public function __construct(\stdClass $msg)
    {
        $this->msg = $msg;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.client.message')
            ->to($this->msg->client_email, $this->msg->client_name)
            ->subject($this->msg->message_subject)
            ->with([
                'content' => $this->msg->message_body,
            ]);
    }
}
