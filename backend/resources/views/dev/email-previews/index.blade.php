<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('email_previews.page_title') }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, sans-serif; max-width: 48rem; margin: 3rem auto; padding: 0 1.5rem; color: #0f172a; }
        h1 { font-size: 1.5rem; margin: 0 0 .5rem; }
        p { color: #475569; margin: 0 0 1.5rem; }
        code { background: #f1f5f9; padding: .1rem .35rem; border-radius: .25rem; font-size: .875em; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: .75rem 1rem; border-bottom: 1px solid #e2e8f0; }
        th { font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; color: #64748b; }
        a { color: #2563eb; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .empty { padding: 2rem; background: #f8fafc; border-radius: .5rem; text-align: center; }
        .empty h2 { margin: 0 0 .5rem; font-size: 1rem; }
    </style>
</head>
<body>
    <h1>{{ __('email_previews.page_title') }}</h1>
    <p>{{ __('email_previews.intro_before') }} <code>emails/config.js</code>{{ __('email_previews.intro_after') }}</p>

    @if ($previews->count() === 0)
        <div class="empty">
            <h2>{{ __('email_previews.empty_heading') }}</h2>
            <p>{{ __('email_previews.empty_body') }}</p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>{{ __('email_previews.table_template') }}</th>
                    <th>{{ __('email_previews.table_action') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($previews as $preview)
                    <tr>
                        <td>
                            <strong>{{ $preview->title }}</strong><br>
                            <code>{{ $preview->template }}</code>
                        </td>
                        <td>
                            <a href="{{ $preview->url }}" target="_blank" rel="noopener">{{ __('email_previews.action_open') }} &rarr;</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
