<?php

namespace App\Modules\Admin\Presentation\Web\Controllers;

class WebAdminDashboardController
{
    public function __invoke()
    {
        return view('operator.admin.dashboard');
    }
}
