<?php

namespace App\Http\Controllers;

use App\Models\RecyclingCenter;
use Illuminate\Http\Request;

class RecyclingCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recyclingCenters = RecyclingCenter::all();
        return view("center.index")->with('recyclingCenters', $recyclingCenters);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("center.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'services' => 'required|array|min:1',
            'address' => 'required',
            'is_dropbox' => 'required',
            'operation_hour' => 'required_if:is_dropbox,0',
        ]);

        $recyclingCenter = new RecyclingCenter;
        $recyclingCenter->name = $request->name;

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
        $recyclingCenter->operation_hour = $request->operation_hour;
        $recyclingCenter->save();

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
        $recyclingCenter = RecyclingCenter::find($id);
        return view("center.edit")->with('recyclingCenter', $recyclingCenter);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'services' => 'required|array|min:1',
            'address' => 'required',
            'is_dropbox' => 'required',
            'operation_hour' => 'required_if:is_dropbox,0',
        ]);

        $recyclingCenter = RecyclingCenter::find($id);
        $recyclingCenter->name = $request->name;

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
        $recyclingCenter->operation_hour = $request->operation_hour;
        $recyclingCenter->update();

        return redirect()->route("center.index");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        RecyclingCenter::find($id)->delete();
        return redirect()->route("center.index");
    }
}
