(function () {
    const consentCookieName = 'atelier_nova_cookie_consent';

    const readCookie = () => {
        const prefix = consentCookieName + '=';
        const cookies = document.cookie ? document.cookie.split('; ') : [];

        for (const cookie of cookies) {
            if (cookie.indexOf(prefix) === 0) {
                return decodeURIComponent(cookie.substring(prefix.length));
            }
        }

        return null;
    };

    const writeCookie = (value) => {
        const expires = new Date();
        expires.setFullYear(expires.getFullYear() + 1);
        const secure = window.location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = consentCookieName + '=' + encodeURIComponent(value) + '; Expires=' + expires.toUTCString() + '; Path=/; SameSite=Lax' + secure;
        
        const banner = document.querySelector('[data-cookie-banner]');
        if (banner) {
            banner.style.display = 'none';
        }
    };

    const banner = document.querySelector('[data-cookie-banner]');
    const acceptButton = document.querySelector('[data-cookie-accept]');
    const declineButton = document.querySelector('[data-cookie-decline]');

    if (banner && readCookie() === null) {
        banner.style.display = 'flex';
    }

    if (acceptButton) {
        acceptButton.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            writeCookie('accepted');
        });
    }

    if (declineButton) {
        declineButton.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            writeCookie('necessary');
        });
    }
})();
