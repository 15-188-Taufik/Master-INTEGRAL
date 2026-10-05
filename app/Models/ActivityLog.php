<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'module',
        'activity',
        'ip_address',
        'user_agent'
    ];

    // Relasi untuk mengetahui siapa pelakunya
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}