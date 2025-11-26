<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
     $vendors = Vendor::all();
     return response()->api(['message' =>
        'Vendors retrieved successfully'],
        $vendors,
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
            $vendor,
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vendor = Vendor::findorFail($id);
        return response()->api(
            'Vendor retrieved successfully',
            $vendor,
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVendorRequest $request, string $id)
    {
        $vendor = Vendor::findorFail($id);
        $vendor->update($request->validated());
        return response()->api(
            'Vendor updated successfully',
            $vendor,
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $vendor = Vendor::findorFail($id);
        $vendor->delete();
        return response()->api(
            'Vendor deleted successfully',
            null,
            200
        );
    }
}
