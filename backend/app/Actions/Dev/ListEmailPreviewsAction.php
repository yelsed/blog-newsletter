<?php

declare(strict_types=1);

namespace App\Actions\Dev;

use App\Data\Dev\EmailPreviewData;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\LaravelData\DataCollection;

class ListEmailPreviewsAction
{
    /** @return DataCollection<int, EmailPreviewData> */
    public function execute(): DataCollection
    {
        $path = (string) config('email_previews.manifest_path');

        if (! is_file($path)) {
            throw new RuntimeException(
                'Email preview manifest not found. Run `npm run deploy` from emails/ to generate it.'
            );
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read email preview manifest at {$path}.");
        }

        /** @var array{templates?: list<string>, variables?: array<string, mixed>} $manifest */
        $manifest = json_decode($contents, true, flags: JSON_THROW_ON_ERROR);

        $templates = $manifest['templates'] ?? [];
        $variables = $manifest['variables'] ?? [];

        $previews = collect($templates)
            ->map(fn (string $slug): EmailPreviewData => new EmailPreviewData(
                template: $slug,
                title: Str::headline($slug),
                url: route('dev.email-previews.show', $slug),
                variables: $variables,
            ))
            ->all();

        return EmailPreviewData::collect($previews, DataCollection::class);
    }
}
