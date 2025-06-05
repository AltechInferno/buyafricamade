<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMailManager extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $array;
    public function __construct($array)
    {
        $this->array = $array;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.contact')
                    ->from($this->array['from'], env('MAIL_FROM_NAME'))
                    ->subject($this->array['subject'])
                    ->with([
                        'name' => $this->array['name'],
                        'email' => $this->array['email'],
                        'phone' => $this->array['phone'],
                        'content' => $this->array['content']
                    ]);
    }
}
