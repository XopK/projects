<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'slug',
        'name',
        'permissions',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_users');
    }
}
