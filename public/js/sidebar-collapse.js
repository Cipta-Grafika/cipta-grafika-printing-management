/**
 * Desktop Sidebar Collapse Toggle
 * -----------------------------------------------------------
 * File ini SENGAJA dipisah dari 2026.js (webpack bundle) supaya
 * tidak perlu mengedit file hasil build yang sudah di-minify.
 *
 * Cara kerja:
 * 1. 2026.js merender ulang .d-topbar secara dinamis (lewat fungsi
 *    o(e) di dalam bundle) setiap kali shell di-render / re-render.
 * 2. Kita pakai MutationObserver untuk mendeteksi kapan .d-topbar
 *    muncul/berubah, lalu menyisipkan tombol toggle ke dalamnya.
 * 3. Tombol toggle menambah/menghapus class "sidebar-collapsed"
 *    di <html>, yang state CSS-nya sudah didefinisikan di style.css
 *    (hanya aktif di breakpoint desktop >1100px, TIDAK memengaruhi
 *    perilaku drawer mobile yang sudah ada).
 * 4. Preferensi disimpan di localStorage (pola sama seperti toggle
 *    dark/light theme yang sudah ada di 2026.js).
 */
(function () {
    "use strict";

    var STORAGE_KEY = "dash26-sidebar";

    var toggleIconSVG =
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' +
        '<rect x="3" y="4" width="18" height="16" rx="2"/>' +
        '<path d="M9 4v16"/>' +
        "</svg>";

    function applySavedState() {
        try {
            var saved = localStorage.getItem(STORAGE_KEY);
            if (saved === "collapsed") {
                document.documentElement.classList.add("sidebar-collapsed");
            }
        } catch (e) {
            /* localStorage tidak tersedia, abaikan */
        }
    }

    function toggleSidebar() {
        var root = document.documentElement;
        var isCollapsed = root.classList.toggle("sidebar-collapsed");

        try {
            localStorage.setItem(
                STORAGE_KEY,
                isCollapsed ? "collapsed" : "expanded",
            );
        } catch (e) {
            /* localStorage tidak tersedia, abaikan */
        }
    }

    function ensureToggleButton(topbar) {
        if (!topbar || topbar.querySelector("[data-sidebar-toggle]")) {
            return;
        }

        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "icon-btn";
        btn.setAttribute("data-sidebar-toggle", "");
        btn.setAttribute("aria-label", "Toggle sidebar");
        btn.innerHTML = toggleIconSVG;

        var hamburger = topbar.querySelector(".hamburger");
        var crumbs = topbar.querySelector(".crumbs");

        if (hamburger && hamburger.parentNode) {
            hamburger.insertAdjacentElement("afterend", btn);
        } else if (crumbs && crumbs.parentNode) {
            crumbs.insertAdjacentElement("beforebegin", btn);
        } else {
            topbar.insertBefore(btn, topbar.firstChild);
        }
    }

    function scanForTopbar() {
        var topbar = document.querySelector(".d-topbar");
        if (topbar) {
            ensureToggleButton(topbar);
        }
    }

    // Klik tombol (event delegation, karena tombol bisa dibuat ulang
    // setiap kali 2026.js merender ulang topbar)
    document.addEventListener("click", function (e) {
        if (e.target.closest("[data-sidebar-toggle]")) {
            e.preventDefault();
            toggleSidebar();
        }
    });

    // Pasang state tersimpan sedini mungkin supaya tidak "flash"
    applySavedState();

    // Pantau perubahan DOM untuk menyisipkan tombol setiap kali
    // .d-topbar dirender ulang oleh 2026.js
    var observer = new MutationObserver(function () {
        scanForTopbar();
    });

    function init() {
        scanForTopbar();
        observer.observe(document.body, {
            childList: true,
            subtree: true,
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
