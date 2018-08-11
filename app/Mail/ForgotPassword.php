<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use Siggy\User;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

	public $user = null;

	public $reset_url = "";
	
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;

		$this->reset_url = url('account/password_reset/'.$user->reset_token);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
		$this->subject('Account password reset');
        return $this->view('emails.account.forgot_password');
    }
}
