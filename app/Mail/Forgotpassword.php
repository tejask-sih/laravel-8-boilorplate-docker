<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Forgotpassword extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $message;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details,$message,$subject)
    {
        $this->details = $details;
        $this->message = $message;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //$from = env('MAIL_FROM');

        //pr($this->message);
        //return $this->from($from)->to($this->details['email'])->subject($this->subject)->view('emails.forgotpasswordmail');

        return $this->subject("forgotPassword")->view('emails.forgotpasswordmail');

    }
}
