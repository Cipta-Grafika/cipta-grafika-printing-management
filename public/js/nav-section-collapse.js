/**
 * Nav Section Collapse (Accordion per Label Sidebar)
 * -----------------------------------------------------------
 * File ini terpisah dari 2026.js (webpack bundle) supaya tidak
 * perlu mengedit file hasil build yang sudah di-minify.
 *
 * Cara kerja:
 * 1. 2026.js merender .d-sidebar secara dinamis lewat fungsi s()
 *    di dalam bundle, menghasilkan beberapa <nav class="nav-section">
 *    yang masing-masing punya satu <div class="nav-label"> di awal,
 *    diikuti oleh .nav-link / .nav-item-group sebagai isinya.
 * 2. Kita pakai MutationObserver untuk mendeteksi kapan .d-sidebar
 *    dirender, lalu:
 *      - Menyisipkan teks label ke dalam <span> + ikon chevron
 *      - Menambahkan listener klik pada label untuk toggle class
 *        "is-collapsed" di elemen induk .nav-section
 * 3. State collapse per section disimpan di localStorage (key
 *    di-slug dari teks label), supaya preferensi user tetap ada
 *    setelah reload / pindah halaman.
 * 4. Section yang memuat link aktif (halaman yang sedang dibuka)
 *    akan otomatis dipaksa terbuka, supaya user tidak kehilangan
 *    orientasi navigasi.
 */
(function () {
    "use strict";

    var STORAGE_KEY = "dash26-nav-sections";

    var chevronSVG =
        '<svg class="nav-label-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '<path d="m9 18 6-6-6-6"/>' +
        "</svg>";

    function slugify(text) {
        return text
            .toString()
            .trim()
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, "-")
            .replace(/^-+|-+$/g, "");
    }

    function readState() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : {};
        } catch (e) {
            return {};
        }
    }

    function writeState(state) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch (e) {
            /* localStorage tidak tersedia, abaikan */
        }
    }

    function sectionHasActiveLink(section) {
        return !!section.querySelector(
            ".nav-link.is-active, .nav-submenu a.is-active, .nav-item-group.is-open",
        );
    }

    function setupSection(section, savedState) {
        var label = section.querySelector(".nav-label");
        if (!label || label.dataset.navToggleInit === "1") {
            return;
        }
        label.dataset.navToggleInit = "1";

        var text = label.textContent.trim();
        var key = slugify(text);

        label.innerHTML =
            '<span class="nav-label-text">' + text + "</span>" + chevronSVG;
        label.setAttribute("role", "button");
        label.setAttribute("tabindex", "0");
        label.setAttribute("aria-expanded", "true");

        var isCollapsed = savedState[key] === true;
        var forceOpen = sectionHasActiveLink(section);

        if (forceOpen) {
            isCollapsed = false;
        }

        applyState(section, label, isCollapsed);

        function toggle() {
            var nowCollapsed = !section.classList.contains("is-collapsed");
            applyState(section, label, nowCollapsed);

            var state = readState();
            state[key] = nowCollapsed;
            writeState(state);
        }

        label.addEventListener("click", toggle);
        label.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                toggle();
            }
        });
    }

    function applyState(section, label, collapsed) {
        section.classList.toggle("is-collapsed", collapsed);
        label.setAttribute("aria-expanded", collapsed ? "false" : "true");
    }

    function scanSidebar() {
        var sidebar = document.querySelector(".d-sidebar");
        if (!sidebar) return;

        var savedState = readState();

        sidebar.querySelectorAll(".nav-section").forEach(function (section) {
            setupSection(section, savedState);
        });
    }

    var observer = new MutationObserver(function () {
        scanSidebar();
    });

    function init() {
        scanSidebar();
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
