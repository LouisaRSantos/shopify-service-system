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

    function getToastContainer() {
        const containerId = 'globalToastContainer';
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement('div');
            container.id = containerId;
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1080';
            document.body.appendChild(container);
        }
        return container;
    }

    function showToast(message, type = 'success') {
        const container = getToastContainer();
        const toastEl = document.createElement('div');
        toastEl.className = 'toast align-items-center text-white border-0';
        toastEl.role = 'alert';
        toastEl.ariaLive = 'assertive';
        toastEl.ariaAtomic = 'true';

        const bgClass = type === 'error' ? 'bg-danger' : 'bg-success';
        const icon = type === 'error' ? '⚠️' : '✔️';

        toastEl.innerHTML = `
            <div class="d-flex ${bgClass} text-white rounded-3 shadow-sm">
                <div class="toast-body">
                    <strong>${icon}</strong> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        container.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 4000 });
        toast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    function initializeCustomerForms() {
        const customerForm = document.getElementById('customerForm');
        if (customerForm && customerForm.dataset.ajaxBound !== 'true') {
            customerForm.dataset.ajaxBound = 'true';
            const btn = document.getElementById('submitBtn');

            customerForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                if (btn) {
                    btn.disabled = true;
                    btn.innerText = 'Processing...';
                }

                const formData = new FormData(customerForm);

                try {
                    const res = await fetch('/customers/store', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await res.json();
                    showToast(data.message, data.status);

                    if (data.status === 'success') {
                        customerForm.reset();
                    }
                } catch (err) {
                    showToast('Network error', 'error');
                }

                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Add Customer';
                }
            });
        }

        const importForm = document.getElementById('importForm');
        if (importForm && importForm.dataset.ajaxBound !== 'true') {
            importForm.dataset.ajaxBound = 'true';

            importForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(importForm);
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                fetch('/customers/import/process', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.created !== undefined) {
                        showToast(`Import completed: ${data.created} created`, 'success');
                    }
                    if (data.failed && data.failed.length > 0) {
                        showToast(`${data.failed.length} rows failed`, 'error');
                        console.log('Failed rows:', data.failed);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Import failed', 'error');
                });
            });
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
                window.__logTableConfig = [];

                $contentElement.html($nextContent.html());
                executeInlineScripts($(doc));

                // re-init dashboard if dashboard is loaded
                const isDashboard = $nextContent.find('#dashboard-recent-customers-body').length > 0;

                if (isDashboard && typeof window.initDashboard === 'function') {
                    if (typeof window.bootDashboard === 'function') {
                        window.bootDashboard();
                    }
                }

                const hasExport =
                    $nextContent.find('#exportType').length ||
                    $nextContent.find('#exportBtn').length;

                if (
                    typeof initializeCustomerExport === 'function' &&
                    hasExport
                ) {
                    initializeCustomerExport();
                }

                if (typeof initLogTables === 'function') {
                    initLogTables();
                }

                initializeCustomerForms();

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
        initializeCustomerForms();
    });
})();