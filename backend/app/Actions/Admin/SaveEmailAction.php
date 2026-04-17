<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Data\Admin\EmailData;
use App\Models\Email;
use App\Models\User;

class SaveEmailAction
{
    public function execute(User $author, EmailData $data, ?Email $email = null): Email
    {
        $attributes = [
            'user_id' => $author->id,
            'subject' => $data->subject,
            'blocks' => array_map(
                static fn ($block): array => $block->toArray(),
                $data->blocks,
            ),
            'status' => $email !== null ? $email->status : $data->status,
        ];

        if ($email === null) {
            return Email::create($attributes);
        }

        $email->fill($attributes)->save();

        return $email;
    }
}
