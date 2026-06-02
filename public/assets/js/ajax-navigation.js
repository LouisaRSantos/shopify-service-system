(function () {
    const contentSelector = '#ajax-content';
    const linkSelector = 'a.ajax-link';

    function isSameOrigin(url) {
        const parsed = new URL(url, window.location.origin);
        return parsed.origin === window.location.origin;
    }

    function updateActiveNavigation(url) {
        document.querySelectorAll(linkSelector).forEach(link => {
            link.classList.toggle('active', link.href === url);
        });
    }

    function findContentElement(doc) {
        return doc.querySelector(contentSelector) || doc.querySelector('.main-panel') || doc.querySelector('.content-wrapper');
    }

    function getCurrentContentElement() {
        return document.querySelector(contentSelector) || document.querySelector('.main-panel');
    }

    function cleanupPageScripts() {
        if (typeof window.__customerExportCleanup === 'function') {
            window.__customerExportCleanup();
        }
    }

    function executeInlineScripts(root) {
        root.querySelectorAll('script').forEach(oldScript => {
            const script = document.createElement('script');
            if (oldScript.src) {
                script.src = oldScript.src;
            } else {
                script.textContent = oldScript.textContent;
            }
            if (oldScript.type) {
                script.type = oldScript.type;
            }
            document.body.appendChild(script).parentNode.removeChild(script);
        });
    }

    function loadPage(url, pushState = false) {
        const contentElement = getCurrentContentElement();
        if (!contentElement) {
            window.location.href = url;
            return;
        }

        cleanupPageScripts();

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html',
            },
            credentials: 'same-origin',
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Failed to load page: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const nextContent = findContentElement(doc);

                if (!nextContent) {
                    throw new Error('AJAX content container not found in response.');
                }

                contentElement.innerHTML = nextContent.innerHTML;
                executeInlineScripts(nextContent);

                const hasExport = nextContent.querySelector('#exportType') || nextContent.querySelector('#exportBtn');
                if (typeof initializeCustomerExport === 'function' && hasExport) {
                    initializeCustomerExport();
                }

                document.title = doc.querySelector('title')?.textContent || document.title;
                updateActiveNavigation(window.location.origin + url);

                if (pushState) {
                    window.history.pushState({ url }, '', url);
                }
            })
            .catch(() => {
                window.location.href = url;
            });
    }

    window.addEventListener('popstate', event => {
        const url = (event.state && event.state.url) || window.location.pathname;
        loadPage(url, false);
    });

    window.addEventListener('DOMContentLoaded', () => {
        document.body.addEventListener('click', event => {
            const link = event.target.closest(linkSelector);
            if (!link) {
                return;
            }

            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            const targetUrl = link.getAttribute('href');
            if (!targetUrl || targetUrl.startsWith('#') || !isSameOrigin(targetUrl)) {
                return;
            }

            event.preventDefault();
            loadPage(targetUrl, true);
        });

        window.history.replaceState({ url: window.location.pathname }, '', window.location.pathname);
        updateActiveNavigation(window.location.href);
    });
})();
