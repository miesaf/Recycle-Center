<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\RecyclingCenter;
use App\Models\User;
use Session;
use Auth;

class DashboardController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function dashboard(Request $request): View
    {
        $totCenters = RecyclingCenter::count();
        $totAdmins = User::where('is_admin', '=', true)->count();
        $totOwners = User::where('is_admin', '=', false)->count();

        if(Auth::user()->is_admin) {
            $recyclingCenters = RecyclingCenter::all();
        } else {
            $recyclingCenters = RecyclingCenter::where("owner", "=", Auth::user()->id)->get();
        }

        return view('dashboard', [
            'user' => $request->user(),
            'totCenters' => $totCenters,
            'totAdmins' => $totAdmins,
            'totOwners' => $totOwners,
            'recyclingCenters' => $recyclingCenters,
        ]);
    }
}
