<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RecyclingCenter;
use App\Models\Review;
use Session;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $owners = User::where("is_center", "=", 1)->get();

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
        $user = User::find($id);

        if ($user) {
            // Delete all recycling centers owned by the user
            $recyclingCenters = RecyclingCenter::where('owner', '=', $id)->get();

            foreach ($recyclingCenters as $center) {
                // Delete all reviews for the recycling center
                Review::where('recycling_center', '=', $center->id)->delete();
            }

            // Now delete the recycling centers
            RecyclingCenter::where('owner', '=', $id)->delete();

            // Finally, delete the user
            if ($user->delete()) {
                Session::flash('success', 'Recycle center owner and related data deleted!');
            } else {
                Session::flash('danger', 'Failed to delete recycle center owner!');
            }
        } else {
            Session::flash('danger', 'Recycle center owner not found!');
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
