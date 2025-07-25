<?php

namespace App\Orchid\Layouts\Group;

use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class GroupListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'groups';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('id', 'ID')
                ->width('80px')
                ->alignCenter(),

            TD::make('preview', 'Изображение')
                ->width('120px')
                ->render(function ($group) {
                    $src = $group->preview ?? $group->video_preview;

                    return "<img src=\"" . e($src) . "\"
                     alt=\"Превью\"
                     style=\"width: 100px; aspect-ratio: 4/3; object-fit: cover;\"
                     class=\"rounded\">";
                }),

            TD::make('video_group', 'Видео')
                ->width('120px')
                ->render(fn($group) => "
                    <a href='{$group->video_group}'
                       target='_blank'
                       style='color: #2563eb; text-decoration: none;'
                       onmouseover='this.style.textDecoration=\"underline\"'
                       onmouseout='this.style.textDecoration=\"none\"'>
                        Ссылка
                    </a>
            "),

            TD::make('title', 'Название')
                ->width('200px')
                ->render(fn($group) => Str::limit($group->title, 30)),

            TD::make('description', 'Описание')
                ->width('250px')
                ->render(fn($group) => Str::limit($group->description, 50)),

            TD::make('date', 'Дата')
                ->width('100px')
                ->render(fn($group) => Carbon::parse($group->date)->format('d.m.Y')),

            TD::make('time', 'Время')
                ->width('80px')
                ->alignCenter()
                ->render(fn($group) => Carbon::parse($group->time)->format('H:i')),

            TD::make('price', 'Цена')
                ->width('100px')
                ->alignRight()
                ->render(fn($group) => number_format($group->price, 0, '', ' ') . ' ₽'),

            TD::make('address_id', 'Адрес')
                ->width('180px')
                ->render(function ($group) {
                    return $group->address
                        ? Str::limit($group->address->studio_address, 30)
                        : 'Адрес отсутствует';
                }),

            TD::make('address_studio', 'Студия')
                ->width('150px')
                ->render(function ($group) {
                    return $group->address
                        ? $group->address->studio_name
                        : 'Cтудия отсутствует';
                }),

            TD::make('level', 'Сложность')
                ->width('150px')
                ->render(fn($group) => $this->parseLevel($group->level)),

            TD::make('class', 'Категория')
                ->width('120px')
                ->render(fn($group) => [
                    'regular_group' => 'Регулярная группа',
                    'course' => 'Курс',
                    'intensive' => 'Интенсив',
                    'class' => 'Класс',
                    'private_lesson' => 'Индивидуальное занятие',
                    'guest_masterclass' => 'Мастер-класс',
                ][$group->class] ?? $group->class),

            TD::make('categories', 'Направленения')
                ->render(function (Group $group) {
                    return $group->categories->pluck('name')->implode(', ');
                }),

            TD::make('schedule', 'Расписание')
                ->width('200px')
                ->alignCenter()
                ->render(fn($group) => $this->parseSchedule($group->schedule)),

            TD::make('active', 'Статус')
                ->width('50px')
                ->alignCenter()
                ->render(fn($group) => $group->active
                    ? '🟢'
                    : '🔴'),

            TD::make('user_id', 'Автор')
                ->width('180px')
                ->render(function ($group) {
                    $user = $group->user;

                    if (!$user) {
                        return '-';
                    }

                    return $user->nickname
                        ? "{$user->name} {$user->nickname}"
                        : $user->name;
                }),

            TD::make('created_at', 'Дата создания')
                ->width('150px')
                ->usingComponent(DateTimeSplit::class),

            TD::make('actions', 'Действия')
                ->alignCenter()
                ->width('100px')
                ->render(fn(Group $group) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([
                        Button::make('Удалить')
                            ->icon('bs.trash3')
                            ->confirm("Вы уверены, что хотите удалить пост <b style='color: red;'>«{$group->title}»</b>")
                            ->method('remove', [
                                'id' => $group->id,
                            ]),

                        Button::make('Заблокировать')
                            ->icon('ban')
                            ->confirm("Вы уверены, что хотите заблокировать пост <b style='color: red;'>«{$group->title}»</b>")
                            ->method('ban', [
                                'id' => $group->id,
                            ]),
                    ])
                ),
        ];
    }


    public function parseLevel($level)
    {
        $levels = json_decode($level, true) ?? [];

        if (!is_array($levels)) {
            return '-';
        }

        $translate = [
            'starter' => 'С нуля',
            'advanced' => 'Средний',
            'beginner' => 'Начинающий',
            'intermediate' => 'Продолжающий',
        ];

        $currentLevel = [];

        foreach ($levels as $key => $value) {
            if ($value == isset($translate[$key])) {
                $currentLevel[] = $translate[$key];
            }
        }

        return implode(', ', $currentLevel);
    }

    public function parseSchedule($schedule)
    {
        $days = json_decode($schedule, true) ?? [];

        if (!is_array($days)) {
            return '-';
        }

        $currentDays = [];

        foreach ($days as $key => $value) {
            if ($value) {
                $currentDays[] = $key;
            }
        }

        return implode(', ', $currentDays);
    }
}

