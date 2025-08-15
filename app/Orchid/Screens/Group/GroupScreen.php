<?php

namespace App\Orchid\Screens\Group;

use App\Models\Group;
use App\Orchid\Layouts\Group\GroupListLayout;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Toast;

class GroupScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'groups' => Group::filters()->orderBy('created_at', 'desc')->paginate(10)
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Посты';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            GroupListLayout::class,
        ];
    }

    public function remove(Group $group)
    {
        $group->delete();

        Toast::info('Пост удалён');
    }

    public function ban(Group $group)
    {
        $group->status_block = !$group->status_block;
        $group->save();
        Toast::warning('Пост заблокирован');
    }

    public function adult(Group $group)
    {
        $group->age_verify = !$group->age_verify;
        $group->save();

        if ($group->age_verify) {
            Toast::info('Вы добавили ограничение 18+ на пост');
        } else {
            Toast::info('Вы сняли ограничение 18+ с поста');
        }
    }
}
