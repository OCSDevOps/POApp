<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Notifications\Messages\MailMessage;

class SupplierResetPasswordNotification extends ResetPasswordNotification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject('Reset Supplier Account Password')
            ->line('You are receiving this email because we received a password reset request for your supplier account.')
            ->action('Reset Password', route('supplier.password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]))
            ->line('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.supplier_users.expire')])
            ->line('If you did not request a password reset, no further action is required.');
    }
}