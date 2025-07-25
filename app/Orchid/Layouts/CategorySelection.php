<?php

namespace App\Orchid\Layouts;

use App\Orchid\Filters\Category\CategoryFilter;
use Orchid\Filters\Filter;
use Orchid\Screen\Layouts\Selection;

class CategorySelection extends Selection
{
    /**
     * @return Filter[]
     */
    public function filters(): iterable
    {
        return [
            CategoryFilter::class,
        ];
    }
}
