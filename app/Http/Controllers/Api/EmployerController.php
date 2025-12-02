<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployerRequest;
use App\Http\Requests\UpdateEmployerRequest;
use App\Http\Resources\EmployerResource;
use App\Models\Employer;
use Illuminate\Http\Request;

class EmployerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employers = Employer::with('country')->get();
        return response()->api(
            'Employers retrieved successfully',
            EmployerResource::collection($employers),
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployerRequest $request)
    {
        $employer = Employer::create($request->validated());
        return response()->api(
            'Employer created successfully',
            new EmployerResource($employer),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employer = Employer::with('country')->findOrFail($id);
        return response()->api(
            'Employer retrieved successfully',
            new EmployerResource($employer),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployerRequest $request, string $id)
    {
        $employer = Employer::findOrFail($id);
        $employer->update($request->validated());
        return response()->api(
            'Employer updated successfully',
            new EmployerResource($employer),
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employer = Employer::findOrFail($id);
        $employer->delete();
        return response()->api(
            'Employer deleted successfully',
            null,
            200
        );
    }
}
