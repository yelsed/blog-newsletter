<?php

declare(strict_types=1);

namespace App\Actions\Newsletter;

use App\Data\Newsletter\SubscribeData;
use App\Models\Subscriber;
use App\Notifications\VerifySubscriptionNotification;
use Illuminate\Support\Facades\Notification;

class SubscribeAction
{
    public function execute(SubscribeData $data): Subscriber
    {
        $subscriber = Subscriber::create([
            'email' => $data->email,
            'name' => $data->name,
        ]);

        Notification::route('mail', $subscriber->email)
            ->notify(new VerifySubscriptionNotification($subscriber));

        return $subscriber;
    }
}
