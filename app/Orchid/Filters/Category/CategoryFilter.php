<?php

namespace App\Orchid\Filters\Category;

use Illuminate\Database\Eloquent\Builder;
use Orchid\Filters\Filter;
use Orchid\Screen\Field;
use Orchid\Screen\Fields\Input;

class CategoryFilter extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Поиск по названию';
    }

    /**
     * The array of matched parameters.
     *
     * @return array|null
     */
    public function parameters(): ?array
    {
        return ['search'];
    }

    /**
     * Apply to a given Eloquent query builder.
     *
     * @param Builder $builder
     *
     * @return Builder
     */
    public function run(Builder $builder): Builder
    {
        $search = $this->request->get('search');

        return $builder->where(function ($query) use ($search) {
            $query->where('name', 'like', "%$search%")
                ->orWhere('slug', 'like', "%$search%");
        });
    }

    /**
     * Get the display fields.
     *
     * @return Field[]
     */
    public function display(): iterable
    {
        return [
            Input::make('search')
                ->type('text')
                ->value($this->request->get('search'))
                ->title('Поиск')
                ->placeholder('Введите название или slug'),
        ];
    }
}
