<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePackageRequest;
use App\Http\Requests\UpdatePackageRequest;
use App\Http\Resources\PackageResource;
use App\Http\Traits\Paginatable;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use Paginatable;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);

        $packages = Package::latest()
            ->paginate($params['per_page']);

        return response()->api(
            'Packages retrieved successfully',
            $this->paginationResponse($packages, PackageResource::class),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePackageRequest $request)
    {
        $package = Package::create($request->validated());
        return response()->api(
            'Package created successfully',
            new PackageResource($package),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $package = Package::findOrFail($id);
        return response()->api(
            'Package retrieved successfully',
            new PackageResource($package),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePackageRequest $request, string $id)
    {
        $package = Package::findOrFail($id);
        $package->update($request->validated());
        return response()->api(
            'Package updated successfully',
            new PackageResource($package),
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $package = Package::findOrFail($id);
        $package->delete();
        return response()->api(
            'Package deleted successfully',
            null,
            200
        );
    }
}
