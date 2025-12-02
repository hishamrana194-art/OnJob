<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CountryResource;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $countries = Country::all();
        return response()->api(
            'Countries retrieved successfully',
            CountryResource::collection($countries),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|array',
            'name.en' => 'required|string|max:255',
            'name.ar' => 'required|string|max:255',
            'code' => 'nullable|string|max:10',
        ]);

        $country = Country::create($validated);
        return response()->api(
            'Country created successfully',
            new CountryResource($country),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $country = Country::findOrFail($id);
        return response()->api(
            'Country retrieved successfully',
            new CountryResource($country),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|array',
            'name.en' => 'sometimes|string|max:255',
            'name.ar' => 'sometimes|string|max:255',
            'code' => 'nullable|string|max:10',
        ]);

        $country = Country::findOrFail($id);
        $country->update($validated);
        return response()->api(
            'Country updated successfully',
            new CountryResource($country),
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $country = Country::findOrFail($id);
        $country->delete();
        return response()->api(
            'Country deleted successfully',
            null,
            200
        );
    }
}
