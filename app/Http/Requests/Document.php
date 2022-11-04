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
            'path' => ['nullable', 'string', new ValidPath],
            's3' => 'required|array',
            's3.key' => 'required|string',
            's3.secret' => 'required|string',
            's3.region' => 'required|string',
            's3.bucket' => 'required|string',
//            's3.url' => 'string',
//            's3.endpoint' => 'string',
//            's3.use_path_style_endpoint' => 'string',
        ];
    }
}
