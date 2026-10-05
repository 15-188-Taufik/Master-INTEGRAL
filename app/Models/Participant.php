<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $table = 'participants';

    protected $fillable = [
        'training_id', 
        'user_id', 
        'nip_nik', 
        'name', 
        'gender', 
        'phone',
        'jabatan', 
        'instansi', 
        'provinsi', 
        'kabupaten_kota', 
        'status_kepegawaian',
    ];

    /**
     * Relasi ke Pelatihan
     */
    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * Relasi Smart Linking ke Akun Pengguna (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
