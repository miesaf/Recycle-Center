<?php

namespace App\Http\Controllers;

use App\Models\RecyclingCenter;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Session;
use Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::user()->id;

        if(Auth::user()->is_admin) {
            $recyclingCenters = RecyclingCenter::with('reviews')->get();
        } elseif(Auth::user()->is_center) {
            $recyclingCenters = RecyclingCenter::where("owner", "=", $userId)->with('reviews')->get();
        } else {
            // Non-admin sees only centers with reviews by the user.
            $recyclingCenters = RecyclingCenter::whereHas('reviews', function ($query) use ($userId) {
                $query->where('user', $userId); // Check if the review exists by the user.
            })
            ->with(['reviews' => function ($query) use ($userId) {
                $query->where('user', $userId); // Load only the user's reviews.
            }])
            ->get();
        }

        return view("review.index")->with('recyclingCenters', $recyclingCenters);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = Auth::user()->id;

        // Fetch recycling centers excluding those already reviewed by the user.
        $recyclingCenters = RecyclingCenter::whereDoesntHave('reviews', function ($query) use ($userId) {
            $query->where('user', $userId); // Check for reviews by the authenticated user.
        })
        ->with('ownerInfo') // Include owner info if needed.
        ->get();

        return view("review.create")->with('recyclingCenters', $recyclingCenters);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recycling_center' => 'required|integer',
            'rating' => 'required|numeric|between:1,5',
            'review' => 'nullable|max:255',
        ]);

        $review = new Review;
        $review->recycling_center = $request->recycling_center;
        $review->user = Auth::user()->id;
        $review->rating = $request->rating;
        $review->review = $request->review;

        if($review->save()) {
            Session::flash('success', 'Recycle center review created!');
        } else {
            Session::flash('danger', 'Failed to create recycle center review!');
        }

        return redirect()->route("review.index");
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
        $review = Review::with('centerInfo')->find($id);
        return view("review.edit")->with('review', $review);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|between:1,5',
            'review' => 'nullable|max:255',
        ]);

        $review = Review::find($id);
        $review->rating = $request->rating;
        $review->review = $request->review;

        if($review->update()) {
            Session::flash('success', 'Recycle center review updated!');
        } else {
            Session::flash('danger', 'Failed to update recycle center review!');
        }

        return redirect()->route("review.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if(Review::find($id)->delete()) {
            Session::flash('success', 'Recycle center review deleted!');
        } else {
            Session::flash('danger', 'Failed to delete recycle center review!');
        }

        return redirect()->route("review.index");
    }

    public function fastReview(string $id)
    {
        $userId = auth()->id(); // Retrieve the authenticated user's ID

        if ($id) {
            // Fetch recycling centers excluding those already reviewed by the user.
            $recyclingCenters = RecyclingCenter::whereDoesntHave('reviews', function ($query) use ($userId) {
                $query->where('user', $userId); // Match reviews by the specific user
            })
            ->where("id", "=", $id) // Only current recycling center
            ->with('ownerInfo') // Include owner info if needed
            ->first();

            if($recyclingCenters) {
                // Return or process the $recyclingCenters as required
                return view('review.fast', compact('recyclingCenters'));
            } else {
                Session::flash('danger', 'Cannot add review to the recycle center!');
                return redirect()->route("review.index");
            }
        } else {
            Session::flash('danger', 'Recycle center not found!');
            return redirect()->route("review.index");
        }
    }

    public function storeFastReview(Request $request, string $id)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|between:1,5',
            'review' => 'nullable|max:255',
        ]);

        $review = new Review;
        $review->recycling_center = $id;
        $review->user = Auth::user()->id;
        $review->rating = $request->rating;
        $review->review = $request->review;

        if($review->save()) {
            Session::flash('success', 'Recycle center review created!');
        } else {
            Session::flash('danger', 'Failed to create recycle center review!');
        }

        return redirect()->route("review.index");
    }
}
