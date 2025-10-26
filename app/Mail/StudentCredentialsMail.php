<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class StudentCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tempPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $tempPassword)
    {
        $this->user = $user;
        $this->tempPassword = $tempPassword;
    }

    /**
     * Build the message.
     */
    public function build()
{
    $fullName = $this->user->name;
    $gradeLevel = $this->user->enrollment->grade_level ?? 'your grade level';

    return $this->subject("Your LSHS Login Credentials - {$fullName} ({$gradeLevel})")
                ->view('emails.student.credentials');
}



}
