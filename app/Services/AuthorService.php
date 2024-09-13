<?php


namespace App\Services;

use App\Models\Author;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class AuthorService
{
    public function getAllAuthors()
    {
        return Author::all();
    }

    public function getAuthorById($id)
    {
        return Author::findOrFail($id);
    }

    public function createAuthor(array $data)
    {
        return Author::create($data);
    }

    public function updateAuthor(Author $author, array $data)
    {
        $author->update($data);
        return $author;
    }

    public function deleteAuthor(Author $author)
    {
        $author->delete();
    }
}
