<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingStage extends Model
{
    // Daftarkan kolom yang diizinkan untuk diisi massal
    protected $fillable = [
        'training_id', 
        'nama_tahapan', 
        'metode', 
        'tgl_mulai', 
        'tgl_selesai'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}