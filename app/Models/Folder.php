<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = [
        'training_id', 'name', 'bidang', 'parent_id', 'user_id',
        'is_public', 'share_token',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function training() { return $this->belongsTo(Training::class); }
    public function files() { return $this->hasMany(File::class); }
    public function parent() { return $this->belongsTo(Folder::class, 'parent_id'); }
    public function children() { return $this->hasMany(Folder::class, 'parent_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
