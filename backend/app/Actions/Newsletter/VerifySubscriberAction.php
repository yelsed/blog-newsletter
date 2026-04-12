<?php

declare(strict_types=1);

namespace App\Actions\Newsletter;

use App\Models\Subscriber;

class VerifySubscriberAction
{
    public function execute(string $token): Subscriber
    {
        $subscriber = Subscriber::where('verification_token', $token)
            ->whereNull('email_verified_at')
            ->firstOrFail();

        $subscriber->update([
            'email_verified_at' => now(),
        ]);

        return $subscriber;
    }
}
