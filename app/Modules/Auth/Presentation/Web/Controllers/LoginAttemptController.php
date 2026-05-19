<?php

namespace App\Modules\Auth\Presentation\Web\Controllers;

use App\Http\Requests\Auth\LoginRequest;

class LoginAttemptController
{
    public function __invoke(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
