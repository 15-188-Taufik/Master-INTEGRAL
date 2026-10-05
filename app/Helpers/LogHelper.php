<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogHelper
{
    /**
     * Mencatat aktivitas admin ke database
     * 
     * @param string $module Nama Modul (Pelatihan, Dokumen, User, dll)
     * @param string $activity Deskripsi aktivitas
     * @return void
     */
    public static function record($module, $activity)
    {
        // Pastikan hanya mencatat jika ada user yang login (menghindari error di public form)
        if (Auth::check()) {
            ActivityLog::create([
                'user_id'    => Auth::id(),
                'module'     => $module,
                'activity'   => $activity,
                'ip_address' => Request::ip(),
                'user_agent' => Request::header('User-Agent'), // Tambahan untuk info browser/perangkat
            ]);
        }
    }
}