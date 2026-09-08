<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    protected array $supportedLocales = ['en', 'am', 'ti'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);
        app()->setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Authenticated user preference
        if (auth()->check() && auth()->user()->preferred_locale) {
            $userLocale = auth()->user()->preferred_locale;
            if (in_array($userLocale, $this->supportedLocales)) {
                return $userLocale;
            }
        }

        // 2. Accept-Language header (for API)
        $headerLocale = $request->getPreferredLanguage($this->supportedLocales);
        if ($headerLocale && in_array($headerLocale, $this->supportedLocales)) {
            return $headerLocale;
        }

        // 3. Session locale (admin panel)
        $sessionLocale = session('locale');
        if ($sessionLocale && in_array($sessionLocale, $this->supportedLocales)) {
            return $sessionLocale;
        }

        return config('app.locale', 'en');
    }
}
