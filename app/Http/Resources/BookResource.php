<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'published_date' => $this->published_date,
            'author_id' => $this->author_id,
            'author' => new AuthorResource($this->whenLoaded('author')),

            'status' => $this->status,
        ];
    }
}
