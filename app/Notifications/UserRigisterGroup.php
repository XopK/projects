<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRigisterGroup extends Notification
{
    use Queueable;

    public $user;
    public $group;

    /**
     * Create a new notification instance.
     */
    public function
    __construct($user, $group)
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
            ->subject('Новый клиент в вашем наборе')
            ->greeting('Здравствуйте!')
            ->line("Пользователь {$this->user->name} записался в ваш набор «{$this->group->title}».")
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
