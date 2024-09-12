<?php

namespace App\Policies;

use App\Models\BorrowRecord;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BorrowRecordPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isLibrarian();
    }

    public function view(User $user, BorrowRecord $borrowRecord)
    {
        return $user->isAdmin() || $user->isLibrarian();
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isLibrarian();
    }
}
