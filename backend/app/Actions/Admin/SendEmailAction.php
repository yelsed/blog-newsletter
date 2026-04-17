<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Enums\EmailStatus;
use App\Models\Email;
use App\Models\Subscriber;
use App\Notifications\ComposedEmailNotification;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Notification;
use Spatie\QueueableAction\QueueableAction;

class SendEmailAction
{
    use QueueableAction;

    public function execute(Email $email): void
    {
        // Atomic draft -> sent transition so a retried job does not re-fan-out.
        $claimed = Email::query()
            ->whereKey($email->id)
            ->where('status', EmailStatus::Draft)
            ->update([
                'status' => EmailStatus::Sent,
                'sent_at' => CarbonImmutable::now(),
            ]);

        if ($claimed === 0) {
            return;
        }

        $email->refresh();

        // TODO: when subscriber count >1k, chunk fan-out into child jobs.
        Subscriber::query()->active()->cursor()->each(
            function (Subscriber $subscriber) use ($email): void {
                Notification::route('mail', $subscriber->email)
                    ->notify(new ComposedEmailNotification($email, $subscriber));
            },
        );
    }
}
