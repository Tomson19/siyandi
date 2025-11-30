<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmNewEmail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $url;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $url)
    {
        $this->user = $user;
        $this->url  = $url;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Konfirmasi Perubahan Email SIYANDI')
            ->view('emails.confirm-new-email');
    }
}
