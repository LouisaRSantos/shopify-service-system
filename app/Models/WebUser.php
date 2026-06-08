<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function exportLogs(): HasMany
    {
        return $this->hasMany(ExportLog::class, 'user_id');
    }

    public function customerActivityLogs(): HasMany
    {
        return $this->hasMany(CustomerActivityLog::class, 'user_id');
    }
}