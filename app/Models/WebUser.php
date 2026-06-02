<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class WebUser extends Authenticatable
{
    protected $table = 'web_users';

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'status',
    ];

    protected $hidden = [
        'password',
    ];
}