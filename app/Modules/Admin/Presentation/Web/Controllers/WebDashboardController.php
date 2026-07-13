<?php

namespace App\Modules\Admin\Presentation\Web\Controllers;

class WebDashboardController
{
    public function __invoke()
    {
        if (auth()->user()->isAdmin() || in_array(auth()->user()->role, ['admin', 'operator', 'manajer_laundry'])) {
            return redirect()->route('admin.dashboard');
        }

        return view('pelanggan.dashboard.index');
    }
}
