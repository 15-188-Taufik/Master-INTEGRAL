<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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

    /**
     * Generator Token 6 digit acak berentropi tinggi & dijamin unik antarkelas (F-09, NF-01)
     */
    public static function generateUniqueToken(): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $maxIndex = strlen($characters) - 1;

        do {
            $token = '';
            for ($i = 0; $i < 6; $i++) {
                $token .= $characters[random_int(0, $maxIndex)];
            }
        } while (self::where('invitation_code', $token)->exists());

        return $token;
    }

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
     * Folder induk arsip dan subfolder dokumen pelatihan.
     */
    public function folders()
    {
        return $this->hasMany(Folder::class);
    }

    /**
     * Trigger 3 — Perhitungan Jadwal Sebar Pasca (F-10):
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
     * Trigger 1 — Otomatis generate Token 6 digit acak & unik saat pelatihan dibuat
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->invitation_code)) {
                $model->invitation_code = self::generateUniqueToken();
            }
        });

        // Trigger 2 — siapkan struktur arsip standar saat pelatihan dibuat.
        static::created(function (Training $training): void {
            $userId = Auth::id() ?: User::query()->value('id');

            if (!$userId) {
                return;
            }

            $root = $training->folders()->firstOrCreate(
                ['parent_id' => null],
                [
                    'name' => $training->nama_pelatihan,
                    'bidang' => $training->bidang,
                    'user_id' => $userId,
                    'is_public' => false,
                ]
            );

            foreach (['JADWAL PELATIHAN', 'DATA PESERTA', 'HASIL EVALUASI DAMPAK'] as $name) {
                $training->folders()->firstOrCreate(
                    ['parent_id' => $root->id, 'name' => $name],
                    [
                        'bidang' => $training->bidang,
                        'user_id' => $userId,
                        'is_public' => false,
                    ]
                );
            }
        });
    }
}