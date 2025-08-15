<?php

namespace App\Orchid\Layouts\Address;

use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Components\Cells\DateTimeSplit;
use Orchid\Screen\Layouts\Table;
use Orchid\Screen\TD;

class AddressListLayout extends Table
{
    /**
     * Data source.
     *
     * The name of the key to fetch it from the query.
     * The results of which will be elements of the table.
     *
     * @var string
     */
    protected $target = 'addresses';

    /**
     * Get the table cells to be displayed.
     *
     * @return TD[]
     */
    protected function columns(): iterable
    {
        return [
            TD::make('studio_name', 'Название студии'),
            TD::make('studio_address', 'Адрес студии'),
            TD::make('created_at', 'Дата создания')
                ->usingComponent(DateTimeSplit::class),
            TD::make('Действия')
                ->align(TD::ALIGN_CENTER)
                ->width('150px')
                ->cantHide()
                ->render(function ($address) {
                    return
                        ModalToggle::make('Редактировать')
                            ->icon('pencil')
                            ->modal('editAddress')
                            ->method('editAddress', ['id' => $address->id])
                            ->modalTitle('Редактировать адрес')
                            ->class('btn btn-sm btn-secondary') .

                        Button::make('Удалить')
                            ->icon('trash')
                            ->confirm("Вы уверены, что хотите удалить адрес «{$address->studio_address}»?")
                            ->method('removeAddress', [
                                'id' => $address->id,
                            ]);


                }),
        ];
    }
}
