<?php

namespace App\Orchid\Screens\Address;

use App\Models\AddressList;
use App\Orchid\Layouts\Address\AddressListLayout;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;

class AddressScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'addresses' => AddressList::filters()->paginate(10),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Адреса';
    }

    public function description(): ?string
    {
        return 'Адреса студий';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Добавить адрес')
                ->modal('addAddress')
                ->method('createAddress')
                ->icon('plus'),
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
            AddressListLayout::class,

            Layout::modal('addAddress', Layout::rows([
                Input::make('studio_name')
                    ->required()
                    ->title('Название студии')
                    ->type('text'),

                Input::make('studio_address')
                    ->required()
                    ->title('Адрес студии')
                    ->type('text'),

            ]))->title('Добавить адрес')->applyButton('Создать')->size('modal-dialog-centered'),

            Layout::modal('editAddress', Layout::rows([
                Input::make('address.studio_name')
                    ->required()
                    ->title('Название студии')
                    ->type('text'),

                Input::make('address.studio_address')
                    ->required()
                    ->title('Адрес студии')
                    ->type('text'),

                Input::make('address.id')
                    ->type('hidden'),
            ]))->title('Редактировать адрес')
                ->applyButton('Сохранить изменения')
                ->size('modal-dialog-centered')
                ->async('asyncGetAddress')
        ];
    }
    public function asyncGetAddress(Request $request): iterable
    {
        $address = AddressList::findOrFail($request->get('id'));

        return [
            'address' => $address,
        ];
    }

    public function editAddress(Request $request): void
    {
        $request->validate([
            'address.studio_name' => 'required|string|max:255',
            'address.studio_address' => 'required|string|max:255',
            'address.id' => 'required|exists:address_lists,id',
        ]);

        $address = AddressList::findOrFail($request->input('address.id'));

        $address->update([
            'studio_name' => $request->input('address.studio_name'),
            'studio_address' => $request->input('address.studio_address'),
        ]);

        Toast::info('Адрес успешно обновлен!');
    }

    public function createAddress(Request $request): void
    {
        $request->validate([
            'studio_name' => 'required|string|max:255',
            'studio_address' => 'required|string|max:255',
        ]);

        AddressList::create([
            'studio_name' => $request->get('studio_name'),
            'studio_address' => $request->get('studio_address'),
        ]);

        Toast::info('Адрес добавлен!');
    }

    public function removeAddress(Request $request): void
    {
        AddressList::findOrFail($request->get('id'))->delete();

        Toast::info('Адрес удален!');
    }
}
