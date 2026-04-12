<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifySubscriptionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Subscriber $subscriber,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $verificationUrl = config('app.frontend_url').'/newsletter/verify?token='.$this->subscriber->verification_token;

        return (new MailMessage)
            ->subject(__('newsletter.verify_subject'))
            ->view('emails.verify-subscription', [
                'appName' => config('app.name'),
                'subscriberName' => $this->subscriber->name ?? __('newsletter.subscriber'),
                'verificationUrl' => $verificationUrl,
            ]);
    }
}
