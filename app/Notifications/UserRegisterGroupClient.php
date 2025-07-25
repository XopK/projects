<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRegisterGroupClient extends Notification
{
    use Queueable;

    public $group;
    public $user;

    public function __construct($group, $user)
    {
        $this->group = $group;
        $this->user = $user;
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

        $dateFormatted = $this->group->date
            ? Carbon::parse($this->group->date)->format('d.m.Y')
            : 'не указана';

        $timeFormatted = $this->group->time
            ? Carbon::parse($this->group->time)->format('H:i')
            : 'не указано';

        return (new MailMessage)
            ->subject('Вы записались в набор')
            ->greeting('Здравствуйте, ' . $this->user->name . '!')
            ->line("Вы успешно записались в набор «{$this->group->title}».")
            ->line("Дата начала: {$dateFormatted} в {$timeFormatted}")
            ->action('Посмотреть детали набора', $url)
            ->line('Спасибо, что пользуетесь нашим сервисом!')
            ->salutation('С уважением, команда ВсеТанцы');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
