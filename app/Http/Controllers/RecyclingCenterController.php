<?php

namespace App\Http\Controllers;

use App\Models\RecyclingCenter;
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
            $recyclingCenters = RecyclingCenter::with('ownerInfo')->get();
        } else {
            $recyclingCenters = RecyclingCenter::where("owner", "=", Auth::user()->id)->get();
        }

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
        if(isset($request->q)) {
            $locations = RecyclingCenter::where('name', 'LIKE', "%$request->q%")
                                        ->orWhere('address', 'LIKE', "%$request->q%")
                                        ->get();
        } else {
            $latitude = $request->latitude; // User's current latitude
            $longitude = $request->longitude; // User's current longitude
            $radius = 5; // Optional radius in kilometers

            $query = RecyclingCenter::selectRaw('*, (6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude)))) AS distance', [
                $latitude, $longitude, $latitude
            ])
            ->orderBy('distance', 'ASC');

            if ($radius) {
                $query->having('distance', '<=', $radius);
            }

            $locations = $query->get();
        }

        return response()->json($locations);
    }
}
