<?php

namespace App\Modules\Customer\Presentation\Web\Controllers;

use App\Models\Notifikasi;
use App\Models\NotifikasiRead;
use App\Models\Pelanggan;
use App\Modules\Order\Application\Services\OrderWebService;
use Illuminate\Http\Request;

class CustomerNotificationController
{
    public function __construct(
        private readonly OrderWebService $orderWebService,
    ) {
    }

    public function index(Request $request)
    {
        $data = $this->orderWebService->notificationData($request->user());

        $pelanggan = Pelanggan::where('user_id', $request->user()->id)->first();

        // Bug fix: jika tidak ada profil pelanggan, tidak tampilkan broadcast karena
        // tidak bisa mark-as-read (akan abort 403), sehingga muncul terus selamanya.
        $broadcasts = $pelanggan ? Notifikasi::query()
            ->where('jenis', Notifikasi::JENIS_JAM_OPERASIONAL)
            ->whereNull('pelanggan_id')
            ->whereNotExists(function ($sub) use ($pelanggan) {
                $sub->selectRaw(1)
                    ->from('notifikasi_reads')
                    ->whereColumn('notifikasi_reads.notifikasi_id', 'notifikasi.id')
                    ->where('notifikasi_reads.pelanggan_id', $pelanggan->id);
            })
            ->latest()
            ->get()
            ->map(fn (Notifikasi $notifikasi) => [
                'id' => $notifikasi->id,
                'category' => 'Info',
                'title' => 'Jam Operasional',
                'message' => $notifikasi->pesan,
                'time' => $notifikasi->created_at->locale('id')->diffForHumans(),
                'timestamp' => $notifikasi->created_at,
                'icon' => 'clock',
                'box_class' => 'bg-[#FEF4E9]',
                'icon_class' => 'text-zyngga-status-warning',
            ]) : collect();

        // Notifikasi personal jam_operasional (ditujukan ke pelanggan tertentu, belum dibaca)
        $personal = $pelanggan ? Notifikasi::query()
            ->where('jenis', Notifikasi::JENIS_JAM_OPERASIONAL)
            ->where('pelanggan_id', $pelanggan->id)
            ->where('is_read', false)
            ->latest()
            ->get()
            ->map(fn (Notifikasi $notifikasi) => [
                'id' => $notifikasi->id,
                'category' => 'Info',
                'title' => 'Jam Operasional',
                'message' => $notifikasi->pesan,
                'time' => $notifikasi->created_at->locale('id')->diffForHumans(),
                'timestamp' => $notifikasi->created_at,
                'icon' => 'clock',
                'box_class' => 'bg-[#FEF4E9]',
                'icon_class' => 'text-zyngga-status-warning',
            ]) : collect();

        $notifications = $data['notifications']
            ->concat($broadcasts)
            ->concat($personal)
            ->sortByDesc(fn (array $notification) => $notification['timestamp'] ?? null)
            ->values();

        return view('pelanggan.notifications.index', ['notifications' => $notifications]);
    }

    public function markAsRead(Request $request, Notifikasi $notifikasi)
    {
        $pelanggan = Pelanggan::where('user_id', $request->user()->id)->first();

        abort_unless(
            $notifikasi->pelanggan_id === null
                || ($pelanggan && $notifikasi->pelanggan_id === $pelanggan->id),
            403
        );

        if ($notifikasi->pelanggan_id === null) {
            // Broadcast: catat per-pelanggan di notifikasi_reads, tabel notifikasi tidak disentuh
            abort_if($pelanggan === null, 403);

            NotifikasiRead::firstOrCreate(
                ['notifikasi_id' => $notifikasi->id, 'pelanggan_id' => $pelanggan->id],
                ['read_at' => now()],
            );
        } else {
            // Personal: update is_read langsung di tabel notifikasi
            $notifikasi->update(['is_read' => true]);
        }

        return back();
    }
}
