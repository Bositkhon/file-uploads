<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchUploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('api')->check();
    }

    public function rules(): array
    {
        return [
            'attachments'   => ['required', 'array', 'max:10'],
            'attachments.*' => ['file', 'max:5120', 'mimetypes:image/jpeg,image/png,application/octet-stream'],
        ];
    }
}
