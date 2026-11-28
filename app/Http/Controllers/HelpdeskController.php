<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Models\HelpdeskTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpdeskController extends Controller
{
    /**
     * Form Helpdesk Kendala Login (Publik — bisa diakses dari halaman login tanpa masuk)
     */
    public function create()
    {
        return view('helpdesk.create');
    }

    /**
     * Simpan Tiket Kendala Login Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip_nik' => 'nullable|string|max:30',
            'email' => 'required|email|max:255',
            'kategori_kendala' => 'required|string|max:100',
            'deskripsi' => 'required|string|min:10',
        ]);

        $ticket = HelpdeskTicket::create([
            'ticket_code' => HelpdeskTicket::generateTicketCode(),
            'nama' => $validated['nama'],
            'nip_nik' => $validated['nip_nik'] ?? null,
            'email' => $validated['email'],
            'kategori_kendala' => $validated['kategori_kendala'],
            'deskripsi' => $validated['deskripsi'],
            'status' => 'open',
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('helpdesk.create')
            ->with('success', 'Tiket bantuan berhasil dikirim dengan Kode Tiket: ' . $ticket->ticket_code . '. Tim Superadmin akan segera menindaklanjuti.');
    }

    /**
     * Daftar & Tindak Lanjut Tiket Helpdesk (Khusus Superadmin)
     */
    public function index(Request $request)
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $tickets = HelpdeskTicket::with('handler')
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('ticket_code', 'ILIKE', "%{$search}%")
                        ->orWhere('nama', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%")
                        ->orWhere('nip_nik', 'ILIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('helpdesk.index', compact('tickets', 'status', 'search'));
    }

    /**
     * Update Status & Catatan Tindak Lanjut Tiket (Khusus Superadmin)
     */
    public function updateStatus(Request $request, HelpdeskTicket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,diproses,selesai',
            'catatan_admin' => 'nullable|string',
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'catatan_admin' => $validated['catatan_admin'] ?? $ticket->catatan_admin,
            'handled_by' => Auth::id(),
        ]);

        LogHelper::record('Helpdesk', 'Memperbarui tiket ' . $ticket->ticket_code . ' menjadi status: ' . $validated['status']);

        return redirect()->back()->with('success', 'Status tiket ' . $ticket->ticket_code . ' berhasil diperbarui.');
    }
}