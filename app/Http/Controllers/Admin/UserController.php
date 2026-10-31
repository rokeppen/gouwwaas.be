<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('gebruikers.index', ['users' => User::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('gebruikers.form', ['user' => new User]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        // The new user picks their own password through the e-mailed link.
        $user = User::create([...$validated, 'password' => Str::random(64)]);
        $user->forcePasswordReset(newAccount: true);

        return redirect()->route('gebruikers.index')
            ->with('status', "{$user->name} is aangemaakt en kreeg een e-mail om een wachtwoord in te stellen.");
    }

    public function edit(User $gebruiker): View
    {
        return view('gebruikers.form', ['user' => $gebruiker]);
    }

    public function update(Request $request, User $gebruiker): RedirectResponse
    {
        $gebruiker->update($request->validate($this->rules($gebruiker)));

        return redirect()->route('gebruikers.index')->with('status', "{$gebruiker->name} is bijgewerkt.");
    }

    public function destroy(Request $request, User $gebruiker): RedirectResponse
    {
        if ($gebruiker->is($request->user())) {
            return back()->withErrors(['user' => 'Je kan je eigen account niet verwijderen.']);
        }

        $gebruiker->endSessions();
        $gebruiker->delete();

        return redirect()->route('gebruikers.index')->with('status', "{$gebruiker->name} is verwijderd.");
    }

    public function forcePasswordReset(User $gebruiker): RedirectResponse
    {
        $gebruiker->forcePasswordReset();

        return back()->with('status', "Het wachtwoord van {$gebruiker->name} is ongeldig gemaakt en er is een e-mail gestuurd naar {$gebruiker->email} om een nieuw wachtwoord te kiezen.");
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($user)],
        ];
    }
}
