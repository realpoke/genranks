<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UploadGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // TODO: Fix mime types and think more about the size
            'file' => 'required|file|max:2048',
            // 'file' => 'required|file|mimes:rep|max:2048',
        ];
    }
}
