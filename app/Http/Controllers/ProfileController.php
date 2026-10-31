<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        return view('profiel', ['user' => $request->user()]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = $request->user();
        $user->update(['password' => $validated['password']]);

        // Stay logged in here, but log out on every other device.
        $request->session()->regenerate();
        $user->endSessions(exceptSessionId: $request->session()->getId());

        return redirect()->route('profiel')->with('status', 'Je wachtwoord is gewijzigd.');
    }
}
