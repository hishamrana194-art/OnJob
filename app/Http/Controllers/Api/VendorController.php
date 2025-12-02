<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = Vendor::all();
        return response()->api(
            'Vendors retrieved successfully',
            VendorResource::collection($vendors),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVendorRequest $request)
    {
        $vendor = Vendor::create($request->validated());
        return response()->api(
            'Vendor created successfully',
            new VendorResource($vendor),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        return response()->api(
            'Vendor retrieved successfully',
            new VendorResource($vendor),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendorRequest $request, string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->validated());
        return response()->api(
            'Vendor updated successfully',
            new VendorResource($vendor),
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();
        return response()->api(
            'Vendor deleted successfully',
            null,
            200
        );
    }
}
