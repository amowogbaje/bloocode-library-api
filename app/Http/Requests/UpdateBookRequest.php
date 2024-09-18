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
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,' . $this->route('book'),
            'published_date' => 'nullable|date',
            'author_id' => 'required|exists:authors,id',
        ];
    }
}
