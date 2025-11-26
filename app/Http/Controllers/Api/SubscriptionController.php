<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::all();
        return response()->api(
            'Subscriptions retrieved successfully',
            $subscriptions,
            200
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request)
    {
        $subscription = Subscription::create($request->validated());
        return response()->api(
            'Subscription created successfully',
            $subscription,
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subscription = Subscription::findorFail($id);
        return response()->api(
            'Subscription retrieved successfully',
            $subscription,
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( UpdateSubscriptionRequest $request, string $id)
    {
        $subscription = Subscription::findorFail($id);
        $subscription->update($request->validated());
        return response()->api(
            'Subscription updated successfully',
            $subscription,
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subscription = Subscription::findorFail($id);
        $subscription->delete();
        return response()->api(
            'Subscription deleted successfully',
            null,
            200
        );
    }
}
