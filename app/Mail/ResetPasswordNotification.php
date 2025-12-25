<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ResetPasswordNotification extends Mailable
{
    
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * The email address of the user.
     *
     * @var string
     */
    public $email;

    /**
     * The application locale.
     *
     * @var string
     */
    public $locale;

    /**
     * Create a new message instance.
     *
     * @param  string  $token
     * @param  string  $email
     * @return void
     */
    /**
     * Create a new notification instance.
     *
     * @param  string  $token
     * @param  string  $email
     * @return void
     */
    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
        $this->locale = app()->getLocale();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.password-reset')
                    ->subject(__('auth.reset_password_email.title', [], $this->locale))
                    ->with([
                        'token' => $this->token,
                        'email' => $this->email,
                        'locale' => $this->locale
                    ]);
    }
}
