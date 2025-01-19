<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Log;
use Session;

class ProfileController extends Controller
{
    public function show(string $id)
    {
        $user = User::withTrashed()->find($id);
        return view("profile.show")->with('user', $user);
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        if ($request->user()->save()) {
            Session::flash('success', 'Profile updated!');

            Log::create([
                'module' => 'Users',
                'model_id' => Auth::user()->id,
                'action' => 'update',
                'user' => Auth::user() ? Auth::user()->id : null,
            ]);
        } else {
            Session::flash('danger', 'Failed to update profile!');
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        Log::create([
            'module' => 'Users',
            'model_id' => $user->id,
            'action' => 'delete',
            'user' => Auth::user() ? Auth::user()->id : null,
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
