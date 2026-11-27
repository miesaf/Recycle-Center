<?php

namespace App\Http\Controllers;

use App\Models\RecyclingCenter;
use App\Models\Review;
use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Session;
use Auth;

class RecyclingCenterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->ensureManager();

        if (Auth::user()->is_admin) {
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
        $this->ensureManager();

        $owners = User::select("id", "name", "email")->where("is_admin", "<>", 1)->get();
        return view("center.create")->with('owners', $owners);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->ensureManager();
        $validated = $request->validate($this->rules());

        $recyclingCenter = new RecyclingCenter;
        $recyclingCenter->name = $request->name;
        $recyclingCenter->phone_no = $request->phone_no;

        $recyclingCenter->services = json_encode(['services' => array_values(array_unique($validated['services']))]);

        $recyclingCenter->address = $request->address;
        $recyclingCenter->is_dropbox = $request->is_dropbox;

        if ($request->is_dropbox) {
            $recyclingCenter->operation_hour = "-";
        } else {
            $recyclingCenter->operation_hour = $request->operation_hour;
        }

        $recyclingCenter->latitude = $request->latitude;
        $recyclingCenter->longitude = $request->longitude;

        if (Auth::user()->is_admin) {
            $recyclingCenter->owner = $request->owner;
        } else {
            $recyclingCenter->owner = Auth::user()->id;
        }

        if ($recyclingCenter->save()) {
            Session::flash('success', 'Recycle center created!');

            Log::create([
                'module' => 'RecyclingCenter',
                'model_id' => $recyclingCenter->id,
                'action' => 'create',
                'user' => Auth::user() ? Auth::user()->id : null,
            ]);
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
        $recyclingCenter = $this->centerFor($id, true);
        $recyclingCenter->load(['ownerInfo' => fn ($query) => $query->withTrashed()]);

        return view("center.show")->with('recyclingCenter', $recyclingCenter);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $recyclingCenter = $this->centerFor($id);
        $owners = User::select("id", "name", "email")->where("is_admin", "<>", 1)->get();
        return view("center.edit")->with('recyclingCenter', $recyclingCenter)->with('owners', $owners);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $recyclingCenter = $this->centerFor($id);
        $validated = $request->validate($this->rules());

        $recyclingCenter->name = $request->name;
        $recyclingCenter->phone_no = $request->phone_no;

        $recyclingCenter->services = json_encode(['services' => array_values(array_unique($validated['services']))]);

        $recyclingCenter->address = $request->address;
        $recyclingCenter->is_dropbox = $request->is_dropbox;

        if ($request->is_dropbox) {
            $recyclingCenter->operation_hour = "-";
        } else {
            $recyclingCenter->operation_hour = $request->operation_hour;
        }

        $recyclingCenter->latitude = $request->latitude;
        $recyclingCenter->longitude = $request->longitude;

        if (Auth::user()->is_admin) {
            $recyclingCenter->owner = $request->owner;
        } else {
            $recyclingCenter->owner = Auth::user()->id;
        }

        if ($recyclingCenter->update()) {
            Session::flash('success', 'Recycle center updated!');

            Log::create([
                'module' => 'RecyclingCenter',
                'model_id' => $recyclingCenter->id,
                'action' => 'update',
                'user' => Auth::user() ? Auth::user()->id : null,
            ]);
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
        $recyclingCenter = $this->centerFor($id);

        if ($recyclingCenter->delete()) {
            // Delete all reviews for the recycling center
            Review::where('recycling_center', '=', $id)->delete();

            Session::flash('success', 'Recycle center deleted!');

            Log::create([
                'module' => 'RecyclingCenter',
                'model_id' => $recyclingCenter->id,
                'action' => 'delete',
                'user' => Auth::user() ? Auth::user()->id : null,
            ]);
        } else {
            Session::flash('danger', 'Failed to delete recycle center!');
        }

        return redirect()->route("center.index");
    }

    public function verify(string $id)
    {
        $recyclingCenter = RecyclingCenter::findOrFail($id);
        $recyclingCenter->is_verified = true;

        if ($recyclingCenter->update()) {
            Session::flash('success', 'Recycle center verified!');

            Log::create([
                'module' => 'RecyclingCenter',
                'model_id' => $recyclingCenter->id,
                'action' => 'verify',
                'user' => Auth::user() ? Auth::user()->id : null,
            ]);
        } else {
            Session::flash('danger', 'Failed to verify recycle center!');
        }

        return redirect()->route("center.index");
    }

    public function getLocations()
    {
        return response()->json(RecyclingCenter::where("is_verified", "=", true)->get());
    }

    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255',
            'f' => 'nullable|array',
            'f.*' => 'string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:0|max:100',
        ]);

        // Only verified centers are public; the name/address match is grouped so an address match
        // cannot bring in an unverified center.
        $query = RecyclingCenter::query()->where('is_verified', '=', true);
        $matchText = function ($query, string $text) {
            $query->where(function ($sub) use ($text) {
                $sub->where('name', 'LIKE', "%{$text}%")->orWhere('address', 'LIKE', "%{$text}%");
            });
        };
        // Each material filter is a bound parameter (it used to be pasted into the SQL).
        $matchServices = function ($query, array $filters) {
            foreach ($filters as $filter) {
                $query->whereRaw("JSON_CONTAINS(services, ?, '$.services')", [json_encode((string) $filter)]);
            }
        };

        if ($request->has('q') && $request->has('f')) {
            // 1. If both `q` and `f` exist, search by name/address and filter by services.
            $matchText($query, (string) $request->q);
            $matchServices($query, (array) $request->f);
            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'DESC');
        } elseif ($request->has('q')) {
            // 2. If `q` is defined, search by name or address and sort by rating.
            $matchText($query, (string) $request->q);
            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'DESC');
        } elseif ($request->has('r')) {
            // 3. If `r` is defined, return the top 10 centers by rating.
            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'DESC')->limit(10);
        } elseif ($request->has('f')) {
            // 4. If 'f' is defined, filter by material and sort by rating.
            $matchServices($query, (array) $request->f);
            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'DESC');
        } else {
            // 5. If no parameters are defined, display centers based on location and sort by rating.
            if ($request->latitude && $request->longitude) {
                $latitude = (float) $request->latitude; // User's latitude
                $longitude = (float) $request->longitude; // User's longitude
                $radius = $request->radius ?? (config('services.search_radius') / 1000); // Optional radius (default 3 km)

                $query->selectRaw('*, (6371 * ACOS(COS(RADIANS(?)) * COS(RADIANS(latitude)) * COS(RADIANS(longitude) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(latitude)))) AS distance', [
                    $latitude,
                    $longitude,
                    $latitude
                ])
                    ->having('distance', '<=', $radius)
                    ->orderBy('distance', 'ASC');
            }

            $query->withAvg('reviews', 'rating')->orderBy('reviews_avg_rating', 'DESC');
        }

        // Ensure all results are limited to a maximum of 10.
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

    // Center pages are for admins and center owners only.
    private function ensureManager(): void
    {
        abort_unless(Auth::user()->is_admin || Auth::user()->is_center, 403);
    }

    // A center an admin may see, or its own owner; anyone else gets 403, a missing one 404.
    private function centerFor($id, bool $withTrashed = false): RecyclingCenter
    {
        $center = ($withTrashed ? RecyclingCenter::withTrashed() : RecyclingCenter::query())->findOrFail($id);
        abort_unless(Auth::user()->is_admin || (int) $center->owner === (int) Auth::id(), 403);

        return $center;
    }

    private function rules(): array
    {
        $rules = [
            'name' => 'required|max:255',
            'phone_no' => 'required|max:20',
            'services' => 'required|array|min:1',
            'services.*' => ['string', Rule::in(RecyclingCenter::SERVICES)],
            'address' => 'required',
            'is_dropbox' => 'required',
            'operation_hour' => 'required_if:is_dropbox,0',
            'latitude' => 'required|numeric|between:-90,90', // Latitude must be between -90 and 90
            'longitude' => 'required|numeric|between:-180,180', // Longitude must be between -180 and 180
        ];

        if (Auth::user()->is_admin) {
            $rules['owner'] = 'required|integer|exists:users,id';
        }

        return $rules;
    }
}
