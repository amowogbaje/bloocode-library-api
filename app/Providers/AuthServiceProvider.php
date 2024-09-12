<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Book;
use App\Models\Author;
use App\Models\BorrowRecord;
use App\Policies\UserPolicy;
use App\Policies\BookPolicy;
use App\Policies\AuthorPolicy;
use App\Policies\BorrowRecordPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Book::class => BookPolicy::class,
        Author::class => AuthorPolicy::class,
        BorrowRecord::class => BorrowRecordPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
