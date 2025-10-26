<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class NewProfessorCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $temporaryPassword;

    public function __construct(User $user, $temporaryPassword)
    {
        $this->user = $user;
        $this->temporaryPassword = $temporaryPassword;
    }

    public function build()
    {
        return $this->subject('Your Professor Account Credentials')
                    ->markdown('emails.professors.credentials');
    }
}
