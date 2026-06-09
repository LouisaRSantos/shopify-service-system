(function () {

    function renderTable(container, columns, data) {
        const tbody = container.querySelector("tbody");
        tbody.innerHTML = "";

        data.forEach(row => {
            let tr = "<tr>";

            columns.forEach(col => {
                let val = row[col] ?? "-";
                // format timestamp-like fields
                if (val && (col.endsWith('_at') || col.endsWith('started_at') || col.endsWith('finished_at') ) ) {
                    try { val = new Date(val).toLocaleString(); } catch(e) {}
                }
                tr += `<td>${val}</td>`;
            });

            tr += "</tr>";
            tbody.innerHTML += tr;
        });
    }

    function renderPagination(container, meta, loadPage) {
        const wrapper = container.querySelector(".log-pagination");
        wrapper.innerHTML = "";

        if (!meta) return;

        if (meta.prev_page_url) {
            const prev = document.createElement("button");
            prev.innerText = "Prev";
            prev.className = "btn btn-sm btn-light me-1";
            prev.onclick = () => loadPage(meta.prev_page_url);
            wrapper.appendChild(prev);
        }

        if (meta.next_page_url) {
            const next = document.createElement("button");
            next.innerText = "Next";
            next.className = "btn btn-sm btn-light me-1";
            next.onclick = () => loadPage(meta.next_page_url);
            wrapper.appendChild(next);
        }
    }

    function buildUrl(api, filters) {
        if (!filters || Object.keys(filters).length === 0) return api;
        // if api already has query string or is absolute page url, append filters appropriately
        const hasQuery = api.indexOf('?') !== -1;
        const params = new URLSearchParams();
        Object.keys(filters).forEach(k => { if (filters[k] !== '') params.append(k, filters[k]); });
        const qs = params.toString();
        if (!qs) return api;
        return hasQuery ? (api + '&' + qs) : (api + '?' + qs);
    }

    function load(api, columns, container, filters) {

        const skeleton = container.querySelector(".log-skeleton");
        const tableWrap = container.querySelector(".log-table-wrapper");

        skeleton.style.display = "block";
        tableWrap.style.display = "none";

        const url = buildUrl(api, filters);

        fetch(url)
            .then(res => res.json())
            .then(res => {

                skeleton.style.display = "none";
                tableWrap.style.display = "block";

                renderTable(tableWrap, columns, res.data || []);
                renderPagination(tableWrap, res, (url) => {
                    // when clicking pagination links, load the provided url (it already contains pagination params)
                    load(url, columns, container, {});
                });
            })
            .catch(err => {
                skeleton.style.display = "none";
                tableWrap.style.display = "block";
                container.querySelector('tbody').innerHTML = '<tr><td colspan="'+columns.length+'">Error loading data</td></tr>';
            });
    }

    function init() {

        const modules = document.querySelectorAll(".log-module");

        modules.forEach((container) => {

            const cfg = window.__logTableConfig.find(c => c.el === container);
            if (!cfg) return;

            // initial load without filters
            load(cfg.api, cfg.columns, container, {});

            // wire up filter UI
            const toggle = container.querySelector('.log-toggle-filter');
            const panel = container.querySelector('.log-filter-panel');
            const applyBtn = container.querySelector('.log-apply-filters');
            const clearBtn = container.querySelector('.log-clear-filters');

            if (toggle && panel) {
                toggle.addEventListener('click', () => {
                    panel.style.display = panel.style.display === 'flex' || panel.style.display === 'block' ? 'none' : 'flex';
                });
            }

            if (applyBtn) {
                applyBtn.addEventListener('click', () => {
                    const inputs = container.querySelectorAll('.log-filter-input');
                    const filters = {};
                    inputs.forEach(inp => { filters[inp.dataset.filterKey] = inp.value.trim(); });
                    load(cfg.api, cfg.columns, container, filters);
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    const inputs = container.querySelectorAll('.log-filter-input');
                    inputs.forEach(inp => inp.value = '');
                    load(cfg.api, cfg.columns, container, {});
                });
            }
        });
    }

    window.initLogTables = init;

})();