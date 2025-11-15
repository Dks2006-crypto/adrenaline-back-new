<?php

namespace App\Observers;

use App\Models\Trainer;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        //
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user)
{
    if ($user->isDirty('role_id') && $user->role?->name === 'trainer') {
        Trainer::updateOrCreate(
            ['user_id' => $user->id],
            ['bio' => '', 'specialization' => '']
        );
    }
}

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
