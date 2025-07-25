<?php

namespace App\Models;

use App\Orchid\Filters\Category\CategoryFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Category extends Model
{
    use AsSource;
    use Filterable;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'slug',
    ];

    protected $allowedSorts = [
        'name',
        'slug',
        'created_at',
    ];

    protected $allowedFilters = [
        CategoryFilter::class,
    ];


    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'category_groups');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(DescTeacher::class, 'category_teachers');
    }
}
