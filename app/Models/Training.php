<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Training extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'nama_pelatihan',
        'invitation_code',
        'link_lms',
        'bidang',
        'model',
        'metode',
        'lokasi',
        'angkatan',
        'jumlah_peserta',
        'jp',
        'tgl_mulai',
        'tgl_selesai',
    ];

    public function getSisaHariAttribute()
    {
        $today = Carbon::now()->startOfDay();
        $end = Carbon::parse($this->tgl_selesai)->startOfDay();
        
        return (int) $today->diffInDays($end, false);
    }

    /**
     * Relasi Tahapan untuk Model Pelatihan Blended
     */
    public function stages()
    {
        return $this->hasMany(TrainingStage::class);
    }

    /**
     * Relasi ke Tabel Participant (Satu Pelatihan memiliki Banyak Peserta)
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    /**
     * Trigger Perhitungan Jadwal Sebar Pasca:
     * - Manajerial: 1 Tahun setelah selesai
     * - Teknis / Lainnya: 4 Bulan setelah selesai
     */
    public function getTglSebarL34Attribute()
    {
        $selesai = Carbon::parse($this->tgl_selesai);

        if ($this->bidang == 'Bidang Pengembangan Kompetensi Manajerial') {
            return $selesai->addYear();
        }

        return $selesai->addMonths(4);
    }

    /**
     * Menghitung Sisa Hari Menuju Jadwal Sebar Evaluasi Pasca
     */
    public function getSisaHariSebarAttribute()
    {
        $today = Carbon::now('Asia/Jakarta')->startOfDay();
        $target = $this->tgl_sebar_l34->startOfDay();

        return (int) $today->diffInDays($target, false);
    }
    
    /**
     * Otomatis generate Token 6 digit acak/unik saat pelatihan dibuat
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->invitation_code)) {
                $model->invitation_code = strtoupper(\Illuminate\Support\Str::random(6));
            }
        });
    }
}