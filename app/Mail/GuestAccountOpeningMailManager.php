<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuestAccountOpeningMailManager extends Mailable
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
        return $this->view('emails.guest_account_opening')
                    ->from($this->array['from'], env('MAIL_FROM_NAME'))
                    ->subject($this->array['subject'])
                    ->with([
                        'email' => $this->array['email'],
                        'password' => $this->array['password']
                    ]);
    }
}
