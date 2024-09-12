<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

use App\Models\Author;
use Illuminate\Http\Request;
use App\Http\Resources\AuthorResource;

class AuthorController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Author::class);
        $authors = Author::all();
        return response()->json([
            'message' => 'Authors retrieved successfully',
            'data' => AuthorResource::collection($authors)
        ], 200);
    }

    public function show($id)
    {
        $author = Author::findOrFail($id);
        $this->authorize('view', $author);
        return response()->json([
            'message' => 'Author retrieved successfully',
            'data' => new AuthorResource($author)
        ], 200);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Author::class);

        $request->validate([
            'name' => 'required|string',
            'bio' => 'nullable|string',
            'birthdate' => 'nullable|date',
        ]);

        $author = Author::create($request->all());
        return response()->json([
            'message' => 'Author created successfully',
            'data' => new AuthorResource($author)
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $author = Author::findOrFail($id);
        $this->authorize('update', $author);

        $request->validate([
            'name' => 'required|string',
            'bio' => 'nullable|string',
            'birthdate' => 'nullable|date',
        ]);

        $author->update($request->all());
        return response()->json([
            'message' => 'Author updated successfully',
            'data' => new AuthorResource($author)
        ], 200);
    }

    public function destroy($id)
    {
        $author = Author::findOrFail($id);
        $this->authorize('delete', $author);

        $author->delete();
        return response()->json([
            'message' => 'Author deleted successfully',
            'data' => null
        ], 200);
    }
}
