<?php

namespace App\Enums;

enum Role: string
{
    case MEMBER = 'Member';
    case ADMIN = 'Admin';
    case LIBRARIAN = 'Librarian';
}
