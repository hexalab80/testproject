<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    //public $paytm;
    public function __construct($user)
    {
      $this->user = $user;
      //$this->paytm = $paytm;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.messageemail')->subject('Install fresh Walk and Earn App');
        //return $this->view('mail.messageemail')->subject('Rejected Amount from Walk & Earn');
    }
}
