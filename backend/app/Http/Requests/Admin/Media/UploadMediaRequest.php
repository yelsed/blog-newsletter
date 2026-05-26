<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Media;

use Illuminate\Foundation\Http\FormRequest;

class UploadMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('emails.manage') ?? false;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120'],
            'emailId' => ['nullable', 'integer', 'exists:emails,id'],
        ];
    }
}
