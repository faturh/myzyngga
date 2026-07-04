<?php

namespace App\Modules\Customer\Presentation\Web\Controllers;

use App\Models\Notifikasi;
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

        $broadcasts = Notifikasi::query()
            ->where('jenis', Notifikasi::JENIS_JAM_OPERASIONAL)
            ->where('is_read', false)
            ->where(function ($query) use ($pelanggan) {
                $query->whereNull('pelanggan_id');
                if ($pelanggan) {
                    $query->orWhere('pelanggan_id', $pelanggan->id);
                }
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
            ]);

        $notifications = $data['notifications']
            ->concat($broadcasts)
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

        $notifikasi->update(['is_read' => true]);

        return back();
    }
}
