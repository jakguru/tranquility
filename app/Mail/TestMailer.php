<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestMailer extends Mailable
{
    use Queueable, SerializesModels;

    protected $my_subject;
    protected $my_content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $content)
    {
        $this->my_subject = $subject;
        $this->my_content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->my_subject)
                    ->markdown('emails.general.test')->with([
                        'subject' => $this->my_subject,
                        'content' => $this->my_content,
                    ]);
    }
}
