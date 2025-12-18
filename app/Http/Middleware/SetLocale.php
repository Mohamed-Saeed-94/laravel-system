<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $availableLocales = config('app.available_locales', []);
        $defaultLocale = config('app.locale');

        $locale = $request->user()?->locale
            ?? $request->session()->get('locale')
            ?? $defaultLocale;

        if (! in_array($locale, $availableLocales, true)) {
            $locale = $defaultLocale;
        }

        app()->setLocale($locale);
        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
