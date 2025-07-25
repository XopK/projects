<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCancelRegistration extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    public $user;
    public $group;

    public function __construct($user, $group)
    {
        $this->user = $user;
        $this->group = $group;
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
        $url = route('group', ['group' => $this->group->id]);

        return (new MailMessage)
            ->subject('Отмена записи в наборе')
            ->greeting('Здравствуйте!')
            ->line("Пользователь {$this->user->name} отменил запись в наборе «{$this->group->title}».")
            ->action('Посмотреть набор', $url)
            ->line('Спасибо, что пользуетесь нашим сервисом!')
            ->salutation('С уважением, команда ВсеТанцы');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
}
