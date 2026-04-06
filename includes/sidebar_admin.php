<!-- includes/sidebar_admin.php -->
<!-- Admin sidebar navigation. Requires $currentPage to be set before including. -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fa-solid fa-comments"></i>
            <span class="sidebar-logo-text">FeedbackPro</span>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
            <i class="fa-solid fa-bars"></i>
        </button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">
            <?= strtoupper(substr($_SESSION['name'] ?? 'A', 0, 1)) ?>
        </div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['name'] ?? '') ?></div>
            <div class="user-role badge badge-admin">Admin</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="/admin/dashboard.php"
           class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge"></i>
            <span>Dashboard</span>
        </a>
        <a href="/admin/feedbacks.php"
           class="nav-item <?= ($currentPage ?? '') === 'feedbacks' ? 'active' : '' ?>">
            <i class="fa-solid fa-comment-dots"></i>
            <span>Feedbacks</span>
        </a>
        <a href="/admin/reports.php"
           class="nav-item <?= ($currentPage ?? '') === 'reports' ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-line"></i>
            <span>Reports</span>
        </a>

        <div class="nav-section-label">Account</div>
        <a href="/auth/logout.php" class="nav-item nav-item-danger">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </nav>
</aside>
