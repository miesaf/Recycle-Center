<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));

        // // Capture the 'remember' input from the login form (checkbox)
        // $remember = $request->boolean('remember'); // Returns true or false

        // // Authenticate the user with 'remember' support
        // if (Auth::attempt($request->only('email', 'password'), $remember)) {
        //     // Regenerate the session to prevent fixation attacks
        //     $request->session()->regenerate();

        //     return redirect()->intended(route('dashboard', absolute: false));
        // }

        // // If authentication fails, redirect back with an error
        // return back()->withErrors([
        //     'email' => __('The provided credentials do not match our records.'),
        // ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
