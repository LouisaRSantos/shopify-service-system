<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerActivityLog extends Model
{
    protected $table = 'customer_activity_logs';

    protected $fillable = [
        'user_id',
        'activity_type',
        'status',
        'payload',
        'response_payload',
        'count_added',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'response_payload' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(WebUser::class, 'user_id');
    }
}