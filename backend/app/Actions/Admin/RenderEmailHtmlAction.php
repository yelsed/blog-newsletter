<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Data\Admin\EmailData;

class RenderEmailHtmlAction
{
    public function execute(EmailData $data, ?string $unsubscribeUrl = null): string
    {
        $blocks = array_map(
            static fn ($block): array => $block->toArray(),
            $data->blocks,
        );

        return (string) view('emails.composed-email', [
            'subject' => $data->subject,
            'blocks' => $blocks,
            'appName' => config('app.name'),
            'unsubscribeUrl' => $unsubscribeUrl ?? '#preview',
        ])->render();
    }
}
