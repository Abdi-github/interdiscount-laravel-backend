<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    private const SUPPORTED_LOCALES = ['de', 'en', 'fr', 'it'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->query('lang')
            ?? $request->header('Accept-Language')
            ?? config('app.locale', 'de');

        // Parse Accept-Language header (take first matching locale)
        if (str_contains($locale, ',')) {
            $locale = $this->parseAcceptLanguage($locale);
        }

        // Normalize to 2-letter code
        $locale = substr($locale, 0, 2);

        if (!in_array($locale, self::SUPPORTED_LOCALES)) {
            $locale = config('app.locale', 'de');
        }

        app()->setLocale($locale);

        return $next($request);
    }

    private function parseAcceptLanguage(string $header): string
    {
        $locales = explode(',', $header);
        foreach ($locales as $locale) {
            $code = substr(trim(explode(';', $locale)[0]), 0, 2);
            if (in_array($code, self::SUPPORTED_LOCALES)) {
                return $code;
            }
        }

        return config('app.locale', 'de');
    }
}
