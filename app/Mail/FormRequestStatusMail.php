<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\ReportCardRequest;

class FormRequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $request;

    /**
     * Create a new message instance.
     */
    public function __construct(ReportCardRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = "Your Report Card Request Has Been " . ucfirst($this->request->status);

        return $this->subject($subject)
                    ->view('emails.form-request-status')
                    ->with(['request' => $this->request]);

    }
}
