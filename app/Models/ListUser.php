<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListUser extends Model
{
    protected $table = 'list_users';

    protected $fillable = [
        'user_id',
        'group_id',
        'status_confirm',
    ];
}
