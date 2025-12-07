<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

trait Paginatable
{
    /**
     * Get pagination parameters from request
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'per_page' => min((int) $request->get('per_page', 15), 100),
            'page' => (int) $request->get('page', 1),
        ];
    }

    /**
     * Build pagination response data
     */
    protected function paginationResponse($paginator, $resourceClass): array
    {
        return [
            'items' => $resourceClass::collection($paginator->items()),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
                'has_more_pages' => $paginator->hasMorePages(),
            ],
        ];
    }
}

