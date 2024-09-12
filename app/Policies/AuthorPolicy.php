<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isLibrarian() || $user->isMember();
    }

    public function view(User $user, Author $author)
    {
        return $user->isAdmin() || $user->isLibrarian() || $user->isMember();
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isLibrarian();
    }

    public function update(User $user, Author $author)
    {
        return $user->isAdmin() || $user->isLibrarian();
    }

    public function delete(User $user, Author $author)
    {
        return $user->isAdmin();
    }
}
