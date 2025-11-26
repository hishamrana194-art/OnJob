<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployerRequest;
use App\Http\Requests\UpdateEmployerRequest;
use App\Models\Employer;
use Illuminate\Http\Request;

class EmployerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employers = Employer::all();
        return response()->api(
            'Employers retrieved successfully',
            $employers,
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store( StoreEmployerRequest $request)
    {
     $employer = Employer::create($request->validated());
     return response()->api(
        'Employer created successfully',
        $employer,
        201
    );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employer = Employer::findorFail($id);
        return response()->api(
            'Employer retrieved successfully',
            $employer,
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployerRequest $request, string $id)
    {
        $employer = Employer::findorFail($id);
        $employer->update($request->validated());
        return response()->api(
            'Employer updated successfully',
            $employer,
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employer = Employer::findorFail($id);
        $employer->delete();
        return response()->api(
            'Employer deleted successfully',
            null,
            200
        );
    }
}
