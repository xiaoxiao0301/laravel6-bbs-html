<?php

namespace App\Observers;

use App\Models\User;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class UserObserver
{
    public function creating(User $user)
    {
        if (empty($user->avatar)) {
            $user->avatar = 'https://s2.ax1x.com/2019/11/21/M5K3pd.jpg';
        }
    }

    public function updating(User $user)
    {
        //
    }
}
