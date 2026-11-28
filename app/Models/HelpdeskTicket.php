<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HelpdeskTicket extends Model
{
    protected $fillable = [
        'ticket_code',
        'nama',
        'nip_nik',
        'email',
        'kategori_kendala',
        'deskripsi',
        'status',
        'catatan_admin',
        'handled_by',
        'ip_address',
    ];

    public static function generateTicketCode(): string
    {
        do {
            $code = 'HLP-' . date('ymd') . '-' . str_pad((string) random_int(100, 999), 3, '0', STR_PAD_LEFT);
        } while (self::where('ticket_code', $code)->exists());

        return $code;
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}