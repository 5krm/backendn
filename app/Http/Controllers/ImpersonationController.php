<?php

namespace App\Http\Controllers;

use App\Models\User;
use Filament\Facades\Filament;

class ImpersonationController extends Controller
{
    public function start(User $user)
    {
        // Security check: Ensure the person hitting this route is allowed to do this
        if (! auth()->check()) {
            abort(403, 'Unauthorized');
        }

        // Capture the manager's ID before switching accounts
        $impeorsonatorId = auth()->id();

        // Log out the manager and cleanly log in the target user using Filament's explicit panel guard
        Filament::auth()->logout();
        session()->invalidate();
        session()->regenerate();

        // save the impersonator ID into the freshly regenerated session
        session(['impersonator_id' => $impeorsonatorId]);

        Filament::auth()->login($user);

        // Redirect to your panel dashboard home
        return redirect()->to('/tutor');
    }

    public function leave()
    {
        if (! session()->has('impersonator_id')) {
            return redirect()->to('/tutor');
        }

        $manager = User::findOrFail(session('impersonator_id'));

        Filament::auth()->logout();
        session()->invalidate();
        session()->regenerate();

        Filament::auth()->login($manager);

        // Send the manager right back to their resource workspace
        return redirect()->to('/tutor/tutors');
    }
}
