<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\SubscriptionResource;
use App\Http\Traits\Paginatable;
use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    use Paginatable;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $params = $this->getPaginationParams($request);

        $subscriptions = Subscription::with(['subscribable', 'package'])
            ->latest()
            ->paginate($params['per_page']);

        return response()->api(
            'Subscriptions retrieved successfully',
            $this->paginationResponse($subscriptions, SubscriptionResource::class),
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
            new SubscriptionResource($subscription->load(['subscribable', 'package'])),
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $subscription = Subscription::with(['subscribable', 'package'])->findOrFail($id);
        return response()->api(
            'Subscription retrieved successfully',
            new SubscriptionResource($subscription),
            200
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionRequest $request, string $id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->update($request->validated());
        return response()->api(
            'Subscription updated successfully',
            new SubscriptionResource($subscription->load(['subscribable', 'package'])),
            200
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();
        return response()->api(
            'Subscription deleted successfully',
            null,
            200
        );
    }
}
