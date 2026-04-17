<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Email;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComposedEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    // TODO: at >5k subscribers swap the Email constructor arg for an int id
    // and re-fetch in toMail() to avoid serialising the full blocks JSON per job.
    public function __construct(
        private readonly Email $email,
        private readonly ?Subscriber $subscriber = null,
        private readonly bool $isTest = false,
    ) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $unsubscribeUrl = $this->isTest || $this->subscriber === null
            ? '#test-preview'
            : config('app.frontend_url').'/newsletter/unsubscribe?token='.$this->subscriber->verification_token;

        return (new MailMessage)
            ->subject($this->email->subject)
            ->view('emails.composed-email', [
                'subject' => $this->email->subject,
                'blocks' => $this->email->blocks,
                'appName' => config('app.name'),
                'unsubscribeUrl' => $unsubscribeUrl,
            ]);
    }
}
