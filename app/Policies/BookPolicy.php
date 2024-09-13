<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin() || $user->isLibrarian() || $user->isMember();
    }

    public function view(User $user, Book $book)
    {
        return $user->isAdmin() || $user->isLibrarian() || $user->isMember();
    }

    public function create(User $user)
    {
        return $user->isAdmin() || $user->isLibrarian();
    }

    public function update(User $user, Book $book)
    {
        return $user->isAdmin() || $user->isLibrarian();
    }

    public function delete(User $user, Book $book)
    {
        return $user->isAdmin();
    }

    public function borrow(User $user, Book $book)
    {
        return ($user->isAdmin() || $user->isMember()) && $book->status === 'Available';
    }

    public function return(User $user, Book $book)
    {
        return ($user->isAdmin() || $user->isMember()) && $book->status === 'Borrowed';
    }
}
