<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'google_id', 'avatar', 'profile_photo', 'whatsapp', 'role', 'bidang',
        'nip_nik', 'gender', 'jabatan', 'instansi', 'provinsi',
        'kabupaten_kota', 'kecamatan', 'kelurahan', 'status_kepegawaian', 'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function participants()
    {
        return $this->hasMany(Participant::class, 'user_id');
    }

    public function trainingHistories()
    {
        return $this->hasMany(Participant::class, 'nip_nik', 'nip_nik');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}