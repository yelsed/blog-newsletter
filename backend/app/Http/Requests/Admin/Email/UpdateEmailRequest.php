<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Email;

use App\Enums\EmailStatus;
use App\Http\Requests\Admin\Email\Concerns\ValidatesEmailBlocks;
use App\Models\Email;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailRequest extends FormRequest
{
    use ValidatesEmailBlocks;

    public function authorize(): bool
    {
        if (! $this->user()?->can('emails.manage')) {
            return false;
        }

        $email = $this->route('email');

        return $email instanceof Email && $email->status === EmailStatus::Draft;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return $this->emailBlockRules();
    }
}
