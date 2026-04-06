/**
 * assets/js/main.js
 * Shared UI behaviour: sidebar toggle, password reveal, mobile menu.
 */

document.addEventListener('DOMContentLoaded', () => {

    /* ── Sidebar toggle (desktop collapse) ──────────────────────────── */
    const sidebar       = document.getElementById('sidebar');
    const mainContent   = document.getElementById('mainContent');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar?.classList.toggle('collapsed');
            mainContent?.classList.toggle('expanded');
        });
    }

    // Mobile overlay menu button
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebar?.classList.toggle('open');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
        if (!sidebar) return;
        const isOpen = sidebar.classList.contains('open');
        if (isOpen && !sidebar.contains(e.target) && e.target !== mobileMenuBtn) {
            sidebar.classList.remove('open');
        }
    });

    /* ── Password visibility toggle ─────────────────────────────────── */
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.closest('.input-icon-right')?.querySelector('input');
            if (!input) return;
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.querySelector('i').className = isText
                ? 'fa-solid fa-eye'
                : 'fa-solid fa-eye-slash';
        });
    });

    /* ── Active nav highlight (based on current path) ───────────────── */
    const path = window.location.pathname;
    document.querySelectorAll('.nav-item').forEach(link => {
        if (link.getAttribute('href') === path) {
            link.classList.add('active');
        }
    });
});
