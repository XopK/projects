<?php

namespace App\Orchid\Screens\Group;

use App\Models\AddressList;
use App\Models\Group;
use App\Models\User;
use Orchid\Screen\Fields\DateTimer;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Relation;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Fields\TextArea;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Screen;

class GroupEditScreen extends Screen
{
    public $group;

    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(Group $group): iterable
    {
        $this->group = $group;
        return [
            'group' => $group,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Редактировать группу: ' . ($this->group->title ?? '');
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([

                Input::make('group.id')
                    ->type('hidden'),

                Input::make('group.title')
                    ->title('Название группы')
                    ->required(),

                Textarea::make('group.description')
                    ->title('Описание группы')
                    ->rows(8)
                    ->maxlength(500)
                    ->required(),

                Input::make('group.time')
                    ->title('Время')
                    ->type('time'),

                DateTimer::make('group.date')
                    ->title('Дата начала')
                    ->format('Y-m-d'),

                DateTimer::make('group.date_end')
                    ->title('Дата окончания')
                    ->format('Y-m-d'),

                Input::make('group.price')
                    ->title('Цена')
                    ->type('number')
                    ->step(0.01),

                Relation::make('group.address_id')
                    ->title('Адрес')
                    ->fromModel(AddressList::class, 'studio_name'),

                Input::make('group.duration')
                    ->title('Длительность')
                    ->type('text'),

                Input::make('group.class')
                    ->title('Класс')
                    ->type('text'),

                Input::make('group.schedule')
                    ->title('Расписание')
                    ->type('text'),

                Switcher::make('group.active')
                    ->title('Активно'),

                Input::make('group.views')
                    ->title('Просмотры')
                    ->type('number'),

                Switcher::make('group.status_block')
                    ->title('Заблокировано'),

                Input::make('group.count_people')
                    ->title('Кол-во людей')
                    ->type('number'),

                Switcher::make('group.age_verify')
                    ->title('Ограничение 18+'),

                Relation::make('group.user_id')
                    ->title('Автор группы')
                    ->fromModel(User::class, 'name'),
            ]),
        ];
    }
}
