<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('resetPassword', ['token' => $this->token, 'email' => $notifiable->email], false));

        return (new MailMessage)
            ->subject('Сброс пароля')
            ->greeting('Здравствуйте!')
            ->line('Вы получили это письмо, потому что была запрошена смена пароля для вашего аккаунта.')
            ->action('Сбросить пароль', $resetUrl)
            ->salutation('С уважением, команда ВсеТанцы')
            ->line('Если вы не запрашивали сброс пароля, просто проигнорируйте это письмо.');

    }

}
