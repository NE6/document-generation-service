<?php

namespace App\Http\Requests;

use App\Rules\ValidPath;
use Illuminate\Foundation\Http\FormRequest;

class Document extends FormRequest
{
    /**
     * Document request validation rules.
     *
     * @return string[]
     */
    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'name' => 'required|string',
            'contents' => 'required',
            'path' => ['nullable', 'string', new ValidPath]
        ];
    }
}
