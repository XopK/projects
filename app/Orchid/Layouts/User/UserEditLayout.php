<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Rows;

class UserEditLayout extends Rows
{
    /**
     * The screen's layout elements.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [
            Input::make('user.name')
                ->type('text')
                ->max(255)
                ->required()
                ->title(__('Name'))
                ->placeholder(__('Name')),

            Input::make('user.nickname')
                ->type('text')
                ->max(255)
                ->required()
                ->title('Фамилия/никнейм')
                ->placeholder(__('Фамилия/никнейм')),

            Input::make('user.email')
                ->type('email')
                ->required()
                ->title(__('Email'))
                ->placeholder(__('Email')),

            Input::make('user.phone')
                ->type('text')
                ->required()
                ->title(__('Телефон'))
                ->placeholder(__('Телефон')),
        ];
    }
}
