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
            'aws_key' => 'nullable|sometimes|string',
            'aws_secret' => 'nullable|sometimes|string',
            'aws_region' => 'nullable|sometimes|string',
            'aws_bucket' => 'nullable|sometimes|string',
        ];
    }
}
