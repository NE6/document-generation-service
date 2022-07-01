<?php

namespace App\Http\Requests;

use App\Rules\ValidPath;
use Illuminate\Foundation\Http\FormRequest;

class DocumentSaveRequest extends FormRequest
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
            'path' => ['nullable', 'string', new ValidPath],
            'key' => 'nullable|sometimes|string',
            'secret' => 'nullable|sometimes|string',
            'region' => 'nullable|sometimes|string',
            'bucket' => 'nullable|sometimes|string',
        ];
    }
}
