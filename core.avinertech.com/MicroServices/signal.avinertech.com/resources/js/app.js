import './bootstrap';

// Set up access token handling for all AJAX requests
document.addEventListener('DOMContentLoaded', function() {
    const accessTokenMeta = document.querySelector('meta[name="access-token"]');
    const accessToken = accessTokenMeta ? accessTokenMeta.getAttribute('content') : '';
    
    if (accessToken) {
        // Set up axios interceptor if axios is available
        if (window.axios) {
            window.axios.defaults.headers.common['Authorization'] = accessToken;
        }
        
        // Set up jQuery AJAX if jQuery is available
        if (window.$ && $.ajaxSetup) {
            $.ajaxSetup({
                headers: {
                    'Authorization': accessToken
                }
            });
        }
        
        // Set up XMLHttpRequest interceptor for vanilla JS
        const originalOpen = XMLHttpRequest.prototype.open;
        XMLHttpRequest.prototype.open = function(method, url, async, user, pass) {
            const result = originalOpen.call(this, method, url, async, user, pass);
            this.setRequestHeader('Authorization', accessToken);
            return result;
        };
        
        // Set up fetch interceptor
        const originalFetch = window.fetch;
        window.fetch = function(input, init = {}) {
            init.headers = init.headers || {};
            if (init.headers instanceof Headers) {
                init.headers.set('Authorization', accessToken);
            } else {
                init.headers['Authorization'] = accessToken;
            }
            return originalFetch.call(this, input, init);
        };
    }
});
