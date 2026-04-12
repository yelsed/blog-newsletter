<?php

declare(strict_types=1);

namespace App\Actions\Newsletter;

use App\Models\Subscriber;

class UnsubscribeAction
{
    public function execute(string $token): Subscriber
    {
        $subscriber = Subscriber::where('verification_token', $token)
            ->whereNotNull('email_verified_at')
            ->whereNull('unsubscribed_at')
            ->firstOrFail();

        $subscriber->update([
            'unsubscribed_at' => now(),
        ]);

        return $subscriber;
    }
}
