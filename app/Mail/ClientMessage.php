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
     * @var \stdClass $client
     */
    protected $client;

    /**
     * Create a new message and client instances.
     *
     * @param \stdClass $msg
     * @param \stdClass $client
     */
    public function __construct(\stdClass $msg, \stdClass $client)
    {
        $this->msg    = $msg;
        $this->client = $client;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.client.message')
            ->to($this->client->email, $this->client->name)
            ->subject($this->msg->subject)
            ->with([
                'content' => $this->msg->body,
            ]);
    }
}
