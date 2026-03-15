<?php

/*
|--------------------------------------------------------------------------
| app/Http/Controllers/UrlController.php
|--------------------------------------------------------------------------
| The controller handles the logic between routes and views.
| Think of it as: Route receives the request → Controller processes it
|                 → returns a View (or a redirect).
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UrlController
{
    /**
     * index()
     * Handles GET /
     * Just shows the homepage view.
     */
    public function index()
    {
        return view('home');
    }

    /**
     * shorten()
     * Handles POST /shorten
     * Validates the URL, generates a short code, stores it, redirects back.
     * In production, you'd store the code-URL mapping in a database instead of session.
     */
    public function shorten(Request $request)
    {
        // validate() automatically redirects back with errors if rules fail.
        // The 'url' rule checks that the value is a valid URL format.
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2048'],
        ]);

        $originalUrl = $validated['url'];

        // Generate a random 6-character short code, e.g. "aB3xQz"
        $code = Str::random(6);

        // Store in session (swap this for a DB insert in production)
        // session()->put(['shorts' => [...existing..., code => url]])
        $shorts = session('shorts', []);
        $shorts[$code] = $originalUrl;
        session(['shorts' => $shorts]);

        // Build the short URL using the app's base URL
        $shortUrl = url("/{$code}");

        // Flash to session so home.blade.php can show the result box
        return redirect()->route('home')->with('short_url', $shortUrl);
    }

    /**
     * redirect()
     * Handles GET /{code}
     * Looks up the code and redirects to the original URL.
     */
    public function redirect(string $code)
    {
        $shorts = session('shorts', []);

        if (isset($shorts[$code])) {
            // 301 = permanent redirect (good for SEO / link sharing)
            return redirect()->away($shorts[$code], 301);
        }

        // 404 if code not found
        abort(404, 'Short link not found.');
    }
}
