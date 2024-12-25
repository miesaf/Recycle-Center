<?php

namespace App\Http\Controllers;

use App\Models\RecyclingCenter;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Session;
use Auth;

class RecyclingCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(Auth::user()->is_admin) {
            $recyclingCenters = RecyclingCenter::with('ownerInfo')->withAvg('reviews', 'rating')->get();
        } else {
            $recyclingCenters = RecyclingCenter::where("owner", "=", Auth::user()->id)->withAvg('reviews', 'rating')->get();
        }

        // Format the average rating to 1 decimal place.
        $recyclingCenters->transform(function ($center) {
            $center->reviews_avg_rating = $center->reviews_avg_rating
                ? number_format($center->reviews_avg_rating, 1)
                : 0;
            return $center;
        });

        return view("center.index")->with('recyclingCenters', $recyclingCenters);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $owners = User::select("id", "name", "email")->where("is_admin", "<>", 1)->get();
        return view("center.create")->with('owners', $owners);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if(Auth::user()->is_admin) {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'phone_no' => 'required|max:20',
                'services' => 'required|array|min:1',
                'address' => 'required',
                'is_dropbox' => 'required',
                'operation_hour' => 'required_if:is_dropbox,0',
                'owner' => 'required|integer|exists:users,id',
                'latitude' => 'required|numeric|between:-90,90', // Latitude must be between -90 and 90
                'longitude' => 'required|numeric|between:-180,180', // Longitude must be between -180 and 180
            ]);
        } else {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'phone_no' => 'required|max:20',
                'services' => 'required|array|min:1',
                'address' => 'required',
                'is_dropbox' => 'required',
                'operation_hour' => 'required_if:is_dropbox,0',
                'latitude' => 'required|numeric|between:-90,90', // Latitude must be between -90 and 90
                'longitude' => 'required|numeric|between:-180,180', // Longitude must be between -180 and 180
            ]);
        }

        $recyclingCenter = new RecyclingCenter;
        $recyclingCenter->name = $request->name;
        $recyclingCenter->phone_no = $request->phone_no;

        // $services = [];
        $services = "{ \"services\" : [";
        foreach ($request->services as $service) {
            // array_push($services, $service);
            $services .= "\"" . $service . "\", ";
        }
        $services = substr($services, 0, -2) . "] }";

        // (sizeof($request->services) > 0) ? null : ($services = "");
        if(sizeof($request->services) < 1) {
            return redirect()->back();
        }

        $recyclingCenter->services = $services;

        $recyclingCenter->address = $request->address;
        $recyclingCenter->is_dropbox = $request->is_dropbox;

        if($request->is_dropbox) {
            $recyclingCenter->operation_hour = "-";
        } else {
            $recyclingCenter->operation_hour = $request->operation_hour;
        }

        $recyclingCenter->latitude = $request->latitude;
        $recyclingCenter->longitude = $request->longitude;

        if(Auth::user()->is_admin) {
            $recyclingCenter->owner = $request->owner;
        } else {
            $recyclingCenter->owner = Auth::user()->id;
        }

        if($recyclingCenter->save()) {
            Session::flash('success', 'Recycle center created!');
        } else {
            Session::flash('danger', 'Failed to create recycle center!');
        }

        return redirect()->route("center.index");
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $recyclingCenter = RecyclingCenter::find($id);
        return view("center.show")->with('recyclingCenter', $recyclingCenter);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $owners = User::select("id", "name", "email")->where("is_admin", "<>", 1)->get();
        $recyclingCenter = RecyclingCenter::find($id);
        return view("center.edit")->with('recyclingCenter', $recyclingCenter)->with('owners', $owners);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        if(Auth::user()->is_admin) {
            $validated = $request->validate([
                'name' => 'required|max:255',
                'phone_no' => 'required|max:20',
                'services' => 'required|array|min:1',
                'address' => 'required',
                'is_dropbox' => 'required',
                'operation_hour' => 'required_if:is_dropbox,0',
                'owner' => 'required|integer|exists:users,id',
                'latitude' => 'required|numeric|between:-90,90', // Latitude must be between -90 and 90
                'longitude' => 'required|numeric|between:-180,180', // Longitude must be between -180 and 180
            ]);
        } else {
            if(Auth::user()->is_admin) {
                $validated = $request->validate([
                    'name' => 'required|max:255',
                    'phone_no' => 'required|max:20',
                    'services' => 'required|array|min:1',
                    'address' => 'required',
                    'is_dropbox' => 'required',
                    'operation_hour' => 'required_if:is_dropbox,0',
                    'latitude' => 'required|numeric|between:-90,90', // Latitude must be between -90 and 90
                    'longitude' => 'required|numeric|between:-180,180', // Longitude must be between -180 and 180
                ]);
            }
        }

        $recyclingCenter = RecyclingCenter::find($id);
        $recyclingCenter->name = $request->name;
        $recyclingCenter->phone_no = $request->phone_no;

        // $services = [];
        $services = "{ \"services\" : [";
        foreach ($request->services as $service) {
            // array_push($services, $service);
            $services .= "\"" . $service . "\", ";
        }
        $services = substr($services, 0, -2) . "] }";

        // (sizeof($request->services) > 0) ? null : ($services = "");
        if(sizeof($request->services) < 1) {
            return redirect()->back();
        }

        $recyclingCenter->services = $services;

        $recyclingCenter->address = $request->address;
        $recyclingCenter->is_dropbox = $request->is_dropbox;

        if($request->is_dropbox) {
            $recyclingCenter->operation_hour = "-";
        } else {
            $recyclingCenter->operation_hour = $request->operation_hour;
        }

        $recyclingCenter->latitude = $request->latitude;
        $recyclingCenter->longitude = $request->longitude;

        if(Auth::user()->is_admin) {
            $recyclingCenter->owner = $request->owner;
        } else {
            $recyclingCenter->owner = Auth::user()->id;
        }

        if($recyclingCenter->update()) {
            Session::flash('success', 'Recycle center updated!');
        } else {
            Session::flash('danger', 'Failed to update recycle center!');
        }

        return redirect()->route("center.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if(RecyclingCenter::find($id)->delete()) {
            // Delete all reviews for the recycling center
            Review::where('recycling_center', '=', $id)->delete();

            Session::flash('success', 'Recycle center deleted!');
        } else {
            Session::flash('danger', 'Failed to delete recycle center!');
        }

        return redirect()->route("center.index");
    }

    public function verify(string $id)
    {
        $recyclingCenter = RecyclingCenter::find($id);
        $recyclingCenter->is_verified = true;

        if($recyclingCenter->update()) {
            Session::flash('success', 'Recycle center verified!');
        } else {
            Session::flash('danger', 'Failed to verify recycle center!');
        }

        return redirect()->route("center.index");
    }

    public function getLocations()
    {
        return response()->json(RecyclingCenter::all());
    }

    public function search(Request $request)
    {
        $query = RecyclingCenter::query();

        if ($request->has('q') && $request->has('f')) {
            // 1. If both `q` and `f` exist, search by name/address and filter by services.
            $filters = $request->f; // Array of materials to filter by

            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('name', 'LIKE', "%{$request->q}%")
                        ->orWhere('address', 'LIKE', "%{$request->q}%");
            });

            // Add where clauses to check if all values exist in the `services` JSON field
            foreach ($filters as $filter) {
                $query->whereRaw("JSON_CONTAINS(services, '\"$filter\"', '$.services')");
            }

            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'DESC');
        } elseif ($request->has('q')) {
            // 2. If `q` is defined, search by name or address and sort by rating.
            $query->where('name', 'LIKE', "%{$request->q}%")
                ->orWhere('address', 'LIKE', "%{$request->q}%");
            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'DESC');
        } elseif ($request->has('r')) {
            // 3. If `r` is defined, return the top 10 centers by rating.
            $query->withAvg('reviews', 'rating')
                ->orderBy('reviews_avg_rating', 'DESC')
                ->limit(10);
        } elseif ($request->has('f')) {
            // 4. If 'f' is defined, filter by material and sort by rating.
            $filters = $request->f;

            foreach ($filters as $filter) {
                $query->whereRaw("JSON_CONTAINS(services, '\"$filter\"', '$.services')");
            }

            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'DESC');
        } else {
            // 5. If no parameters are defined, display centers based on location and sort by rating.
            if ($request->latitude && $request->longitude) {
                $latitude = $request->latitude; // User's latitude
                $longitude = $request->longitude; // User's longitude
                $radius = $request->radius ?? 3; // Optional radius (default 3 km)

                $query->selectRaw('*, (6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude)))) AS distance', [
                    $latitude, $longitude, $latitude
                ])
                ->having('distance', '<=', $radius)
                ->orderBy('distance', 'ASC');
            }

            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'DESC');
        }

        // Ensure all results are limited to a maximum of 15.
        $locations = $query->limit(10)->get();

        // Format the average rating to 1 decimal place.
        $locations->transform(function ($location) {
            $location->reviews_avg_rating = $location->reviews_avg_rating
                ? number_format($location->reviews_avg_rating, 1)
                : null;
            return $location;
        });

        return response()->json($locations);
    }
}
