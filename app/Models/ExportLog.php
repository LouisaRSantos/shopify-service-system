<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    protected $table = 'export_logs';

    protected $fillable = [
        'user_id',
        'export_type',
        'status',
        'payload',
        'response_payload',
        'file_name',
        'rows_count',
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