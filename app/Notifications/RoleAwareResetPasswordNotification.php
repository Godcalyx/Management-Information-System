<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class RoleAwareResetPasswordNotification extends ResetPassword
{
    public function __construct(string $token, protected ?string $explicitRole = null)
    {
        parent::__construct($token);
    }

    public function toMail($notifiable)
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line('This password reset link will expire in '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }

    protected function resetUrl($notifiable): string
    {
        $routeName = match ($this->explicitRole ?? $notifiable->role ?? null) {
            'student' => 'student.password.reset',
            'professor' => 'professor.password.reset',
            'admin', 'superadmin' => 'admin.password.reset',
            default => 'password.reset',
        };

        return url(route($routeName, [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
    }
}
