<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailActiveAccount extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $numberCode;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($numberCode, $userName)
    {
        $this->numberCode = $numberCode;
        $this->userName = $userName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('nguyenhoang080721@gmail.com')
            ->view('mails.mail_active')
            ->subject('Email active account')->with($this->numberCode, $this->userName);
    }
}
