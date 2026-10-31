<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

/**
 * Every logged-in user can create activities; only the owner (or an admin)
 * can change one or see who registered.
 */
class ActivityPolicy
{
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Activity $activity): bool
    {
        return $user->is_admin || $activity->user_id === $user->id;
    }

    public function delete(User $user, Activity $activity): bool
    {
        return $this->update($user, $activity);
    }

    public function viewRegistrations(User $user, Activity $activity): bool
    {
        return $this->update($user, $activity);
    }
}
