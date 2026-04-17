<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Email;

use App\Http\Requests\Admin\Email\Concerns\ValidatesEmailBlocks;
use Illuminate\Foundation\Http\FormRequest;

class PreviewEmailRequest extends FormRequest
{
    use ValidatesEmailBlocks;

    public function authorize(): bool
    {
        return $this->user()?->can('emails.manage') ?? false;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return $this->emailBlockRules();
    }
}
