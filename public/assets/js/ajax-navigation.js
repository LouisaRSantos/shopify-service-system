(function () {

    const contentSelector = '#ajax-content';
    const linkSelector = 'a.ajax-link';

    function isSameOrigin(url) {
        const parsed = new URL(url, window.location.origin);
        return parsed.origin === window.location.origin;
    }

    function updateActiveNavigation(url) {
        const parsedUrl = new URL(url, window.location.origin);
        const currentPathname = parsedUrl.pathname;
        
        $(linkSelector).each(function () {
            const linkUrl = new URL(this.href, window.location.origin);
            const linkPathname = linkUrl.pathname;
            const isActive = linkPathname === currentPathname;
            $(this).toggleClass('active', isActive);
        });
    }

    function findContentElement(doc) {
        return $(doc).find(contentSelector).first().length
            ? $(doc).find(contentSelector).first()
            : $(doc).find('.main-panel').first().length
                ? $(doc).find('.main-panel').first()
                : $(doc).find('.content-wrapper').first();
    }

    function getCurrentContentElement() {
        return $(contentSelector).length
            ? $(contentSelector)
            : $('.main-panel').first();
    }

    function cleanupPageScripts() {
        if (typeof window.__customerExportCleanup === 'function') {
            window.__customerExportCleanup();
        }
    }

    function executeInlineScripts(root) {
        $(root).find('script').each(function () {
            const $oldScript = $(this);
            const script = document.createElement('script');
            if ($oldScript.attr('src')) {
                script.src = $oldScript.attr('src');
            } else {
                script.text = $oldScript.html();
            }

            if ($oldScript.attr('type')) {
                script.type = $oldScript.attr('type');
            }

            document.body.appendChild(script);
            document.body.removeChild(script);
        });
    }

    function loadPage(url, pushState = false) {
        const $contentElement = getCurrentContentElement();
        if (!$contentElement.length) {
            window.location.href = url;
            return;
        }
        cleanupPageScripts();

        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            },
            success: function (html) {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const $nextContent = findContentElement(doc);
                if (!$nextContent.length) {
                    throw new Error('AJAX content container not found in response.');
                }

                $contentElement.html($nextContent.html());
                executeInlineScripts($nextContent);
                const hasExport =
                    $nextContent.find('#exportType').length ||
                    $nextContent.find('#exportBtn').length;

                if (
                    typeof initializeCustomerExport === 'function' &&
                    hasExport
                ) {
                    initializeCustomerExport();
                }

                const pageTitle = $(doc).find('title').text();

                if (pageTitle) {
                    document.title = pageTitle;
                }
                updateActiveNavigation(window.location.origin + url);
                if (pushState) {
                    window.history.pushState({ url }, '', url);
                }
            },
            error: function () {
                window.location.href = url;
            }
        });
    }

    $(window).on('popstate', function (event) {
        const url =
            (event.originalEvent.state &&
                event.originalEvent.state.url)
            || window.location.pathname;
        loadPage(url, false);
    });

    $(function () {
        $(document).on('click', linkSelector, function (event) {
            if (
                event.isDefaultPrevented() ||
                event.button !== 0 ||
                event.metaKey ||
                event.ctrlKey ||
                event.shiftKey ||
                event.altKey
            ) {
                return;
            }
            const targetUrl = $(this).attr('href');
            if (
                !targetUrl ||
                targetUrl.startsWith('#') ||
                !isSameOrigin(targetUrl)
            ) {
                return;
            }
            event.preventDefault();
            loadPage(targetUrl, true);
        });
        window.history.replaceState(
            { url: window.location.pathname },
            '',
            window.location.pathname
        );
        updateActiveNavigation(window.location.href);
    });
})();