<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'isbn' => 'sometimes|required|string|unique:books,isbn,' . $this->route('book'),
            'published_date' => 'nullable|date',
            'author_id' => 'sometimes|required|exists:authors,id',
            'status' => 'sometimes|required|in:Available,Borrowed',
        ];
    }
}
