<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfigLog extends Model
{
    protected $table = 'system_config_logs';

    public $timestamps = false;

    protected $fillable = [
        'setting_key',
        'old_value',
        'new_value',
        'changed_by',
        'changed_at',
    ];
}