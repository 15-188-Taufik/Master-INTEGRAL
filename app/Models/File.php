<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'folder_id', 'display_name', 'file_path', 'file_type', 'file_size', 'user_id',
    ];

    public function folder() { return $this->belongsTo(Folder::class); }
    public function user() { return $this->belongsTo(User::class); }
}
