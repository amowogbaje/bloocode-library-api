<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'bio' => 'nullable|string',
            'birthdate' => 'nullable|date',
        ];
    }

    public function authorize()
    {
        return true;
    }
}
