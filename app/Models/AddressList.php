<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class AddressList extends Model
{
    use AsSource;
    use Filterable;

    protected $table = 'address_lists';

    protected $fillable = [
        'studio_name',
        'studio_address',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'address_id');
    }
}
