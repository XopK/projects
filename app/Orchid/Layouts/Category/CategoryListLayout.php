<?php

namespace App\Orchid\Layouts\Category;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class CategoryListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'categories';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('name', 'Название категории')
                ->sort()
                ->align(TD::ALIGN_LEFT),

            TD::make('slug', 'Название категории в системе')
                ->sort()
                ->defaultHidden(),

            TD::make('created_at', 'Дата создания')
                ->usingComponent(DateTimeSplit::class)
                ->sort(),

            TD::make('Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('150px')
                ->cantHide()
                ->render(function ($category) {
                    return
                        Button::make('Удалить')
                            ->icon('trash')
                            ->confirm("Вы уверены, что хотите удалить категорию «{$category->name}»?")
                            ->method('deleteCategory', [
                                'id' => $category->id,
                            ]);
                }),
        ];
    }

}
