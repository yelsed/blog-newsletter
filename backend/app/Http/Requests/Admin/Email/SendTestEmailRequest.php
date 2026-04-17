<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Email;

use Illuminate\Foundation\Http\FormRequest;

class SendTestEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('emails.send') ?? false;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'email' => ['sometimes', 'email', 'max:255'],
        ];
    }
}
