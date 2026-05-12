<?php

namespace App\Modules\Admin\Presentation\Web\Controllers;

class WebDashboardController
{
    public function __invoke()
    {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('pelanggan.dashboard.index');
    }
}
