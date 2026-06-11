<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUsageLog extends Model
{
    protected $table = 'api_logs';

    protected $fillable = [
        'user_id',
        'method',
        'endpoint',
        'action',
        'response_status',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(WebUser::class, 'user_id');
    }
}
