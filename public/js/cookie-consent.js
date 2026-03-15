/**
 * cookie-consent.js
 * ------------------
 * Vanilla JS Cookie Consent Manager
 * No frameworks. No libraries. Just the browser APIs.
 *
 * CONCEPTS YOU'LL LEARN HERE:
 *  - document.cookie (reading & writing cookies)
 *  - DOM manipulation (querySelector, classList, createElement)
 *  - Event listeners
 *  - Lazy loading (injecting <script>/<iframe> only after consent)
 */

// ─────────────────────────────────────────────
// 1. COOKIE HELPERS
//    The browser's document.cookie API is awkward,
//    so we wrap it in two clean functions.
// ─────────────────────────────────────────────

/**
 * setCookie(name, value, days)
 * Writes a cookie that lives for `days` days.
 *
 * The "path=/" part means the cookie is available
 * on every page of the site, not just the current URL.
 */
function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000); // convert days → milliseconds
    document.cookie = `${name}=${value}; expires=${expires.toUTCString()}; path=/; SameSite=Lax`;
}

/**
 * getCookie(name)
 * Reads a cookie by name. Returns its value, or null if it doesn't exist.
 *
 * document.cookie returns ALL cookies as one string like:
 *   "theme=dark; cookie_consent=accepted; session=abc123"
 * So we split on "; " and search for the one we want.
 */
function getCookie(name) {
    const cookies = document.cookie.split('; ');

    for (let i = 0; i < cookies.length; i++) {
        const [key, value] = cookies[i].split('='); // destructuring: splits "key=value"
        if (key === name) {
            return value;
        }
    }

    return null; // not found
}


// ─────────────────────────────────────────────
// 2. THE BANNER
//    Show / hide the cookie consent banner.
// ─────────────────────────────────────────────

function showBanner() {
    const banner = document.getElementById('cookie-banner');
    if (banner) {
        // Small timeout so the CSS transition fires on page load
        setTimeout(() => banner.classList.add('cookie-banner--visible'), 200);
    }
}

function hideBanner() {
    const banner = document.getElementById('cookie-banner');
    if (banner) {
        banner.classList.remove('cookie-banner--visible');
        // Remove from DOM after the CSS fade-out animation finishes (400ms)
        setTimeout(() => banner.remove(), 400);
    }
}


// ─────────────────────────────────────────────
// 3. LAZY CONTENT LOADER
//    This is the KEY idea of cookie consent:
//    ads, videos, tracking scripts are NOT loaded
//    until the user accepts. We inject them into
//    the DOM dynamically only after consent.
// ─────────────────────────────────────────────

/**
 * loadConsentedContent()
 * Finds every element with data-consent-src and injects
 * the appropriate tag (iframe or script) into it.
 *
 * In your Blade template you'll write something like:
 *   <div data-consent-src="https://youtube.com/embed/xyz" data-consent-type="iframe"></div>
 *
 * That placeholder sits invisible in the HTML until this
 * function runs and breathes life into it.
 */
function loadConsentedContent() {
    const placeholders = document.querySelectorAll('[data-consent-src]');

    placeholders.forEach(function (placeholder) {
        const src  = placeholder.dataset.consentSrc;   // data-consent-src="..."
        const type = placeholder.dataset.consentType;  // data-consent-type="iframe" or "script"

        if (type === 'iframe') {
            const iframe = document.createElement('iframe');
            iframe.src             = src;
            iframe.allowFullscreen = true;
            iframe.loading         = 'lazy';
            iframe.classList.add('consent-iframe');
            placeholder.appendChild(iframe);
            placeholder.classList.add('consent-loaded');

        } else if (type === 'script') {
            const script = document.createElement('script');
            script.src   = src;
            script.async = true;
            document.body.appendChild(script); // scripts go on body
        }

        // Remove the data attributes so we don't load twice
        placeholder.removeAttribute('data-consent-src');
        placeholder.removeAttribute('data-consent-type');
    });
}


// ─────────────────────────────────────────────
// 4. ACCEPT / DECLINE HANDLERS
//    Wired to the buttons in cookie-banner.blade.php
// ─────────────────────────────────────────────

function acceptCookies() {
    setCookie('cookie_consent', 'accepted', 365); // remember for 1 year
    hideBanner();
    loadConsentedContent();                        // NOW load the videos/ads
}

function declineCookies() {
    setCookie('cookie_consent', 'declined', 30);  // remember choice for 30 days
    hideBanner();
    // We do NOT call loadConsentedContent() — media stays blocked
}


// ─────────────────────────────────────────────
// 5. INIT — runs when the page is ready
// ─────────────────────────────────────────────

/**
 * DOMContentLoaded fires when the HTML is fully parsed.
 * (Faster than "load", which waits for images too.)
 */
document.addEventListener('DOMContentLoaded', function () {
    const consent = getCookie('cookie_consent');

    if (consent === 'accepted') {
        // Returning visitor who already said yes → load content silently
        loadConsentedContent();

    } else if (consent === 'declined') {
        // Returning visitor who said no → do nothing, banner stays gone

    } else {
        // First-time visitor → show the banner
        showBanner();
    }

    // Wire up the buttons
    const acceptBtn  = document.getElementById('cookie-accept');
    const declineBtn = document.getElementById('cookie-decline');

    if (acceptBtn)  acceptBtn.addEventListener('click', acceptCookies);
    if (declineBtn) declineBtn.addEventListener('click', declineCookies);
});
