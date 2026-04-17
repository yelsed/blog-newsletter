<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromHeader
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var list<string> $available */
        $available = (array) config('localization.available', []);

        $requested = $request->getPreferredLanguage($available);

        if (is_string($requested) && in_array($requested, $available, true)) {
            App::setLocale($requested);
        }

        return $next($request);
    }
}
