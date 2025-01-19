<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\Log;
use Session;
use Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = User::where("is_admin", "=", 1)->whereNot("id", "=", 1)->get();

        return view("admin.index")->with('admins', $admins);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'phone_no' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $admin = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'password' => Hash::make($request->password),
            'is_admin' => true,
            'is_verified' => false,
        ]);

        if ($admin) {
            Session::flash('success', 'Admin created!');

            Log::create([
                'module' => 'Admins',
                'model_id' => $admin->id,
                'action' => 'create',
                'user' => Auth::user() ? Auth::user()->id : null,
            ]);
        } else {
            Session::flash('danger', 'Failed to create admin!');
        }

        return Redirect::route('admin.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = User::where("id", "=", $id)->first();
        return view("admin.show")->with('admin', $admin);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $admin = User::where("id", "=", $id)->first();
        return view("admin.edit")->with('admin', $admin);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'phone_no' => 'required|string|max:20',
        ]);

        $admin = User::find($id);
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->phone_no = $request->phone_no;

        if ($admin->update()) {
            Session::flash('success', 'Admin updated!');

            Log::create([
                'module' => 'Admins',
                'model_id' => $admin->id,
                'action' => 'update',
                'user' => Auth::user() ? Auth::user()->id : null,
            ]);
        } else {
            Session::flash('danger', 'Failed to update admin!');
        }

        return Redirect::route("admin.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = User::find($id);

        if ($admin->delete()) {
            Session::flash('success', 'Admin deleted!');

            Log::create([
                'module' => 'Admins',
                'model_id' => $admin->id,
                'action' => 'delete',
                'user' => Auth::user() ? Auth::user()->id : null,
            ]);
        } else {
            Session::flash('danger', 'Failed to delete admin!');
        }

        return Redirect::route("admin.index");
    }
}
