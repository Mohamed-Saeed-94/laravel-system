<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Switch application locale for the authenticated user or current session.
     */
    public function switch(string $locale, Request $request): RedirectResponse
    {
        $availableLocales = config('app.available_locales', []);

        if (! in_array($locale, $availableLocales, true)) {
            $locale = config('app.locale');
        }

        if ($user = $request->user()) {
            $user->forceFill(['locale' => $locale])->save();
        }

        $request->session()->put('locale', $locale);
        app()->setLocale($locale);

        return redirect()->intended($request->headers->get('referer') ?? '/');
    }
}
