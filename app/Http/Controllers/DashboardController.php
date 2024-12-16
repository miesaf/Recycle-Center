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

        if (Auth::user()->is_center) {
            // Fetch only centers owned by the current user with their average rating.
            $recyclingCenters = RecyclingCenter::where("owner", "=", Auth::user()->id)
                ->withAvg('reviews', 'rating') // Include average rating.
                ->get();
        } else {
            // Fetch all centers with their average rating.
            $recyclingCenters = RecyclingCenter::withAvg('reviews', 'rating')->get();
        }

        // Format the average rating to 1 decimal place.
        $recyclingCenters->transform(function ($center) {
            $center->reviews_avg_rating = $center->reviews_avg_rating
                ? number_format($center->reviews_avg_rating, 1)
                : 0;
            return $center;
        });

        return view('dashboard', [
            'user' => $request->user(),
            'totCenters' => $totCenters,
            'totAdmins' => $totAdmins,
            'totOwners' => $totOwners,
            'recyclingCenters' => $recyclingCenters,
        ]);
    }
}
