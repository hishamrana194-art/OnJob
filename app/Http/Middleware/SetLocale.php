<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported locales — anything outside this list falls back to Arabic.
     */
    private const SUPPORTED = ['ar', 'en'];
    private const DEFAULT   = 'ar';

    /**
     * Read the Accept-Language request header and set the application locale
     * so that Spatie Translatable (and any __() calls) return the correct language.
     *
     * Header examples:
     *   Accept-Language: ar-SA   → ar
     *   Accept-Language: ar      → ar
     *   Accept-Language: en-US   → en
     *   Accept-Language: en      → en
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Read header — fall back to the default locale
        $header = $request->header('Accept-Language', self::DEFAULT);

        // 2. Extract the primary subtag (e.g. "ar" from "ar-SA,ar;q=0.9,en;q=0.8")
        //    We only care about the very first tag before any comma or semicolon.
        $primary = strtolower(strtok(trim($header), ',; '));

        // 3. Reduce to a 2-letter code (e.g. "ar-sa" → "ar")
        $lang = substr($primary, 0, 2);

        // 4. Validate — unknown locales fall back to the default
        if (!in_array($lang, self::SUPPORTED, true)) {
            $lang = self::DEFAULT;
        }

        // 5. Apply to the application — all subsequent Spatie Translatable
        //    getTranslation() calls will use this locale automatically.
        App::setLocale($lang);

        return $next($request);
    }
}
