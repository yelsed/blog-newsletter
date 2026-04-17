<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\Email;
use App\Notifications\ComposedEmailNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\QueueableAction\QueueableAction;

class SendTestEmailAction
{
    use QueueableAction;

    public function execute(Email $email, string $targetEmail): void
    {
        Notification::route('mail', $targetEmail)
            ->notify(new ComposedEmailNotification($email, isTest: true));
    }
}
