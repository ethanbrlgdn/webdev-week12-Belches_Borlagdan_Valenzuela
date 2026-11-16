<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // user_id has no rule here, so a client can't sneak it in
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:to-read,reading,finished'],
            'rating' => ['nullable', 'integer', 'between:1,5'],
        ];
    }
}