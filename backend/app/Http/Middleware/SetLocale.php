<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales.
     */
    protected array $supportedLocales = ['fr', 'en'];

    /**
     * Handle an incoming request.
     *
     * Detects locale from Accept-Language header or X-Locale custom header.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->detectLocale($request);
        App::setLocale($locale);

        $response = $next($request);

        // Add Content-Language header to response
        if ($response instanceof Response) {
            $response->headers->set('Content-Language', $locale);
        }

        return $response;
    }

    /**
     * Detect the locale from request headers.
     */
    protected function detectLocale(Request $request): string
    {
        // Priority 1: X-Locale custom header
        $xLocale = $request->header('X-Locale');
        if ($xLocale && in_array($xLocale, $this->supportedLocales)) {
            return $xLocale;
        }

        // Priority 2: Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            $locale = $this->parseAcceptLanguage($acceptLanguage);
            if ($locale) {
                return $locale;
            }
        }

        // Fallback to default locale
        return config('app.locale', 'fr');
    }

    /**
     * Parse Accept-Language header and return best matching locale.
     */
    protected function parseAcceptLanguage(string $header): ?string
    {
        // Parse Accept-Language header (e.g., "fr-FR,fr;q=0.9,en;q=0.8")
        $locales = [];
        $parts = explode(',', $header);

        foreach ($parts as $part) {
            $part = trim($part);
            $quality = 1.0;

            if (str_contains($part, ';q=')) {
                [$locale, $q] = explode(';q=', $part);
                $quality = (float) $q;
            } else {
                $locale = $part;
            }

            // Extract base language (e.g., "fr" from "fr-FR")
            $baseLocale = explode('-', $locale)[0];

            if (in_array($baseLocale, $this->supportedLocales)) {
                $locales[$baseLocale] = max($locales[$baseLocale] ?? 0, $quality);
            }
        }

        if (empty($locales)) {
            return null;
        }

        // Sort by quality and return best match
        arsort($locales);

        return array_key_first($locales);
    }
}
