<?php

namespace App\Mail;

use App\Models\FormRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FormRequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $formRequest;

    public function __construct(FormRequest $formRequest)
    {
        $this->formRequest = $formRequest;
    }

    public function build()
    {
        $status = ucfirst($this->formRequest->status);

        return $this->subject("Your Form Request has been {$status}")
                    ->markdown('emails.form_request_status');
    }
}
