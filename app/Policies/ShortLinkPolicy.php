<?php

namespace App\Policies;

use App\Models\ShortLink;
use App\Models\User;

class ShortLinkPolicy
{
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function view(User $user, ShortLink $link): bool
    {
        return $link->user_id === $user->id;
    }

    public function update(User $user, ShortLink $link): bool
    {
        return $link->user_id === $user->id;
    }

    public function delete(User $user, ShortLink $link): bool
    {
        return $link->user_id === $user->id;
    }
}
