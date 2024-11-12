<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use Session;

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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
            'is_verified' => false,
        ]);

        if($user) {
            Session::flash('success', 'Admin created!');
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
        //
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
        ]);

        $admin = User::find($id);
        $admin->name = $request->name;
        $admin->email = $request->email;

        if($admin->update()) {
            Session::flash('success', 'Admin updated!');
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
        if(User::find($id)->delete()) {
            Session::flash('success', 'Admin deleted!');
        } else {
            Session::flash('danger', 'Failed to delete admin!');
        }

        return Redirect::route("admin.index");
    }
}
