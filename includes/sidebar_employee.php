<!-- includes/sidebar_employee.php -->
<!-- Employee sidebar navigation. Requires $currentPage to be set before including. -->
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
            <?= strtoupper(substr($_SESSION['name'] ?? 'E', 0, 1)) ?>
        </div>
        <div class="user-info">
            <div class="user-name"><?= htmlspecialchars($_SESSION['name'] ?? '') ?></div>
            <div class="user-role badge badge-employee">Employee</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="/employee/dashboard.php"
           class="nav-item <?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
            <i class="fa-solid fa-gauge"></i>
            <span>Dashboard</span>
        </a>
        <a href="/employee/submit_feedback.php"
           class="nav-item <?= ($currentPage ?? '') === 'submit' ? 'active' : '' ?>">
            <i class="fa-solid fa-paper-plane"></i>
            <span>Submit Feedback</span>
        </a>
        <a href="/employee/my_feedbacks.php"
           class="nav-item <?= ($currentPage ?? '') === 'my_feedbacks' ? 'active' : '' ?>">
            <i class="fa-solid fa-list-check"></i>
            <span>My Feedbacks</span>
        </a>

        <div class="nav-section-label">Account</div>
        <a href="/auth/logout.php" class="nav-item nav-item-danger">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </nav>
</aside>
