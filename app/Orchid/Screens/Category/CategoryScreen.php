<?php

namespace App\Orchid\Screens\Category;

use App\Models\Category;
use App\Orchid\Layouts\Category\CategoryListLayout;
use App\Orchid\Layouts\CategorySelection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\ModalToggle;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;

class CategoryScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'categories' => Category::filters()->paginate(10)
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Категории';
    }

    public function description(): ?string
    {
        return 'Категории для наборов';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Создать категорию')
                ->modal('addCategory')
                ->method('createCategory')
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
            CategorySelection::class,
            CategoryListLayout::class,

            Layout::modal('addCategory', Layout::rows([
                Input::make('name')
                    ->required()
                    ->title('Название категории')
                    ->type('text'),

            ]))->title('Создать категорию')->applyButton('Создать')->size('modal-dialog-centered'),

            Layout::modal('editCategory', Layout::rows([
                Input::make('category.name')
                    ->required()
                    ->title('Название категории')
                    ->type('text'),

                Input::make('category.id')
                    ->type('hidden'),
            ]))->title('Редактировать категорию')
                ->applyButton('Сохранить изменения')
                ->size('modal-dialog-centered')
                ->async('asyncGetCategory'),
        ];
    }

    public function asyncGetCategory(Request $request): iterable
    {
        $category = Category::findOrFail($request->get('id'));

        return [
            'category' => $category,
        ];
    }

    public function editCategory(Request $request): void
    {
        $request->validate([
            'category.name' => 'required|string|max:255',
            'category.id' => 'required|exists:categories,id',
        ]);

        $category = Category::findOrFail($request->input('category.id'));

        $category->update([
            'name' => $request->input('category.name'),
            'slug' => Str::slug($request->input('category.name')),
        ]);

        Toast::info('Категория обновлена!');
    }

    public function createCategory(Request $request): void
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::updateOrCreate([
            'name' => $request->get('name'),
            'slug' => Str::slug($request->get('name')),
        ]);

        Toast::info('Категория создана!');
    }

    public function deleteCategory(Request $request)
    {
        Category::findOrFail($request->get('id'))->delete();

        Toast::info('Категория удалена!');
    }

}
