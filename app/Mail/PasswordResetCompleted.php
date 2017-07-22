<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetCompleted extends Mailable
{
    use Queueable, SerializesModels;

	public $user = null;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$this->subject('Password successfully reset');
        return $this->view('emails.account.password_reset_complete');
    }
}
