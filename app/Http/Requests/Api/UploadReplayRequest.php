<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class UploadReplayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // TODO: Fix mime types and think more about the size
            'replay' => 'required|file|max:2048',
            // 'replay' => 'required|file|mimes:rep|max:2048',
        ];
    }
}
