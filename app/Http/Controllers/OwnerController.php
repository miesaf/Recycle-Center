<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Session;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $owners = User::where("is_admin", "<>", 1)->get();

        return view("owner.index")->with('owners', $owners);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        $owner = User::where("id", "=", $id)->first();
        return view("owner.edit")->with('owner', $owner);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
            'phone_no' => 'required|max:20',
        ]);

        $owner = User::find($id);
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->phone_no = $request->phone_no;

        if($owner->update()) {
            Session::flash('success', 'Recycle center owner updated!');
        } else {
            Session::flash('danger', 'Failed to update recycle center owner!');
        }

        return redirect()->route("owner.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(User::find($id)->delete()) {
            Session::flash('success', 'Recycle center owner deleted!');
        } else {
            Session::flash('danger', 'Failed to delete recycle center owner!');
        }

        return redirect()->route("owner.index");
    }

    public function verify(string $id)
    {
        $user = User::where("id", "=", $id)->first();
        $user->is_verified = true;

        if($user->update()) {
            Session::flash('success', 'Recycle center owner verified!');
        } else {
            Session::flash('danger', 'Failed to verify recycle center owner!');
        }

        return redirect()->route("owner.index");
    }
}
