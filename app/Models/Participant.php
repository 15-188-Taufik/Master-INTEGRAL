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
        'kecamatan',
        'kelurahan',
        'status_kepegawaian',
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}