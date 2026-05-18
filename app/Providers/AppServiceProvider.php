<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Observers\UserObserver;
use App\Policies\CoursePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Response::macro('api', function ($message, $data, $status = 200) {
            return Response::json([
                'status' => $status,
                'message' => $message,
                'data' => $data,
            ], $status);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Policies
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);

        // Observers
        User::observe(UserObserver::class);
    }
}
