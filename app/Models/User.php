<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name', 'username', 'google_id', 'avatar', 'profile_photo', 'whatsapp', 'role', 'bidang', 
        'nip_nik', 'gender', 'jabatan', 'instansi', 'provinsi', 
        'kabupaten_kota', 'status_kepegawaian', 'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke riwayat keikutsertaan pelatihan peserta
     */
    public function participants()
    {
        return $this->hasMany(Participant::class, 'user_id');
    }

    /**
     * Relasi untuk riwayat pelatihan berdasarkan NIP
     */
    public function trainingHistories()
    {
        return $this->hasMany(Participant::class, 'nip_nik', 'nip_nik');
    }

    /**
     * Relasi ke Audit Trail / Log Aktivitas
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}