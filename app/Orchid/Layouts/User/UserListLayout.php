<?php

declare(strict_types=1);

namespace App\Orchid\Layouts\User;

use App\Models\User;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Layouts\Persona;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class UserListLayout extends Table
{
    /**
     * @var string
     */
    public $target = 'users';

    /**
     * @return TD[]
     */
    public function columns(): array
    {
        return [
            TD::make('name', __('Name'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(function (User $user) {
                    $presenter = $user->presenter();

                    return <<<HTML
<style>
    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 10px;
    }
    .user-row {
        display: flex;
        align-items: center;
    }
    .user-info {
        display: flex;
        flex-direction: column;
    }
    .user-name {
        font-weight: bold;
    }
    .user-role {
        font-size: 0.875rem;
        color: #6b7280;
    }
</style>
<div class="user-row">
    <img src="{$presenter->image()}" class="avatar" alt="Avatar">
    <div class="user-info">
        <div class="user-name">{$presenter->title()}</div>
        <div class="user-role">{$presenter->subTitle()}</div>
    </div>
</div>
HTML;
                }),

            TD::make('email', __('Email'))
                ->sort()
                ->cantHide()
                ->filter(Input::make())
                ->render(fn(User $user) => ModalToggle::make($user->email)
                    ->modal('editUserModal')
                    ->modalTitle($user->presenter()->title())
                    ->method('saveUser')
                    ->asyncParameters([
                        'user' => $user->id,
                    ])),

            TD::make('phone', 'Телефон')
                ->align(TD::ALIGN_LEFT)
                ->filter(Input::make()),

            TD::make('created_at', __('Created'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->defaultHidden()
                ->sort(),

            TD::make('updated_at', __('Last edit'))
                ->usingComponent(DateTimeSplit::class)
                ->align(TD::ALIGN_RIGHT)
                ->sort(),

            TD::make(__('Actions'))
                ->align(TD::ALIGN_CENTER)
                ->width('100px')
                ->render(fn(User $user) => DropDown::make()
                    ->icon('bs.three-dots-vertical')
                    ->list([

                        Link::make(__('Edit'))
                            ->route('platform.systems.users.edit', $user->id)
                            ->icon('bs.pencil'),

                        Button::make(__('Delete'))
                            ->icon('bs.trash3')
                            ->confirm(__('Once the account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                            ->method('remove', [
                                'id' => $user->id,
                            ]),
                    ])),
        ];
    }
}
