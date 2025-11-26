// Clickable Rows
// Makes table rows with `data-detail-url` clickable while ignoring action buttons/links.
(function () {
    function navigate(url, ev) {
        if (!url) return;
        try {
            if (
                ev &&
                (ev.ctrlKey || ev.metaKey || ev.shiftKey || ev.button === 1)
            ) {
                window.open(url, "_blank");
            } else {
                window.location.href = url;
            }
        } catch (e) {
            window.location.href = url;
        }
    }

    function initClickableRows(options = {}) {
        const selector = options.selector || "[data-detail-url]";
        const ignoreSelector =
            options.ignoreSelector ||
            "a, button, .no-row-navigation, input, select, textarea";

        document.addEventListener("click", function (e) {
            const row = e.target.closest(selector);
            if (!row) return;
            if (e.target.closest(ignoreSelector)) return; // clicked an interactive element
            const url =
                row.getAttribute("data-detail-url") || row.dataset.detailUrl;
            if (!url) return;
            e.preventDefault();
            navigate(url, e);
        });

        // keyboard: Enter to activate focused row
        document.addEventListener("keydown", function (e) {
            if (e.key !== "Enter") return;
            const el = document.activeElement;
            if (!el) return;
            const row = el.closest(selector);
            if (!row) return;
            if (el.closest(ignoreSelector)) return;
            const url =
                row.getAttribute("data-detail-url") || row.dataset.detailUrl;
            if (!url) return;
            navigate(url, e);
        });

        // make rows focusable for accessibility
        document.querySelectorAll(selector).forEach((r) => {
            if (!r.hasAttribute("tabindex")) r.setAttribute("tabindex", "0");
            // add pointer cursor via inline style if not already set
            const cur = window.getComputedStyle(r).cursor;
            if (!cur || cur === "auto") r.style.cursor = "pointer";
        });
    }

    // Auto-init on DOM ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", function () {
            initClickableRows();
        });
    } else {
        initClickableRows();
    }

    // Expose for manual init if needed
    window.ClickableRows = { init: initClickableRows };
})();
