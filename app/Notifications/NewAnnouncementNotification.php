<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewAnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $announcement;

    public function __construct($announcement)
    {
        $this->announcement = $announcement;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('📢 New Announcement: ' . $this->announcement->title)
        ->greeting('Hello ' . $notifiable->name . ',')
        ->line('A new announcement has been posted:')
        ->line('**' . $this->announcement->title . '**')
        ->line($this->announcement->content)
        ->when($this->announcement->attachment, function ($mail) {
            $mail->line('An attachment is available with this announcement.')
                 ->action('View Announcement', url('/student/announcements')); // Adjust the URL as needed
        })
        ->line('Thank you for staying informed!')
        ->salutation('— Laboratory Science High School');
}
}
