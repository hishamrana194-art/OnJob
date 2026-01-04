<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantScope
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Determine tenant type and ID based on user_type and role
            if ($user->user_type === 'employer_user') {
                $request->attributes->set('tenant_type', 'employer');
                $request->attributes->set('tenant_id', $user->employer_id);
                $request->attributes->set('is_super_admin', false);

                // Check if user is employer admin
                $isAdmin = $user->role === 'employer_admin' || $user->hasRole('employer_admin');
                $request->attributes->set('is_tenant_admin', $isAdmin);
            } elseif ($user->user_type === 'vendor_user') {
                $request->attributes->set('tenant_type', 'vendor');
                $request->attributes->set('tenant_id', $user->vendor_id);
                $request->attributes->set('is_super_admin', false);

                // Check if user is vendor admin
                $isAdmin = $user->role === 'vendor_admin' || $user->hasRole('vendor_admin');
                $request->attributes->set('is_tenant_admin', $isAdmin);
            } elseif ($user->user_type === 'training_provider') {
                $request->attributes->set('tenant_type', 'training_provider');
                $request->attributes->set('tenant_id', $user->id);
                $request->attributes->set('is_super_admin', false);
                $request->attributes->set('is_tenant_admin', true);
            } else {
                // Super admin or other roles - can see everything
                $request->attributes->set('tenant_type', null);
                $request->attributes->set('tenant_id', null);
                $request->attributes->set('is_super_admin', true);
                $request->attributes->set('is_tenant_admin', true);
            }
        } else {
            // Unauthenticated - set defaults
            $request->attributes->set('tenant_type', null);
            $request->attributes->set('tenant_id', null);
            $request->attributes->set('is_super_admin', false);
            $request->attributes->set('is_tenant_admin', false);
        }

        return $next($request);
    }
}

