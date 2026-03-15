{{--
    resources/views/partials/cookie-banner.blade.php
    -------------------------------------------------
    This partial is included in the main layout (app.blade.php).
    It renders the banner HTML — but it starts INVISIBLE.
    The JS in cookie-consent.js decides whether to show it.

    WHY A PARTIAL?
    In Laravel, @include('partials.cookie-banner') lets you
    drop this banner into any layout without copy-pasting HTML.
    One file to update → changes everywhere.
--}}

<div id="cookie-banner" class="cookie-banner" role="dialog" aria-live="polite" aria-label="Cookie consent">

    <div class="cookie-banner__icon" aria-hidden="true">🍪</div>

    <div class="cookie-banner__body">
        <p class="cookie-banner__title">We use cookies</p>
        <p class="cookie-banner__text">
            We'd like to load embedded videos and advertising content to keep this service running.
            These are only activated after you give consent.
            Read our <a href="/cookie-policy">Cookie Policy</a> for details.
        </p>
    </div>

    <div class="cookie-banner__actions">
        {{-- These IDs are what cookie-consent.js looks for --}}
        <button id="cookie-accept"  class="btn btn--accept">Accept</button>
        <button id="cookie-decline" class="btn btn--decline">Decline</button>
    </div>

</div>
