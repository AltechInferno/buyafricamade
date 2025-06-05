<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountOpeningByAdminEmailManager extends Mailable
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
        return $this->view('emails.account_opening')
                    ->from($this->array['from'], env('MAIL_FROM_NAME'))
                    ->subject($this->array['subject'])
                    ->with([
                        'user_type' => $this->array['user_type'],
                        'password' => $this->array['password']
                    ]);
    }
}
