<?php

namespace App\Modules\Auth\Presentation\Web\Controllers;

use Illuminate\Http\Request;

class SendEmailVerificationNotificationController
{
    public function __invoke(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
