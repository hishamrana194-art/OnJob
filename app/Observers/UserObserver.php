<?php

namespace App\Observers;

use App\Models\Employer;
use App\Models\User;
use App\Models\Vendor;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Note: Seats are incremented in UserController to avoid double counting
     * This is a backup in case user is created outside of the controller
     */
    public function created(User $user): void
    {
        // Seats increment is handled in UserController::store()
        // to have better control and error handling
    }

    /**
     * Handle the User "deleted" event.
     * Automatically decrement seats when a user is deleted
     */
    public function deleted(User $user): void
    {
        $this->decrementSeatForUser($user);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        $this->decrementSeatForUser($user);
    }

    /**
     * Decrement seat count for the user's organization
     */
    protected function decrementSeatForUser(User $user): void
    {
        if ($user->user_type === 'employer_user' && $user->employer_id) {
            $employer = Employer::find($user->employer_id);
            $subscription = $employer?->activeSubscription;

            if ($subscription) {
                $subscription->decrementSeats();
            }
        } elseif ($user->user_type === 'vendor_user' && $user->vendor_id) {
            $vendor = Vendor::find($user->vendor_id);
            $subscription = $vendor?->activeSubscription;

            if ($subscription) {
                $subscription->decrementSeats();
            }
        }
        // Training providers don't count towards seats
    }
}
