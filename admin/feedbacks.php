<?php
/**
 * admin/feedbacks.php
 * Admin page listing all feedback with search/filter.
 * Feedback rows are loaded via AJAX (GET /api/get_feedbacks.php).
 */

$currentPage = 'feedbacks';
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedbacks – FeedbackPro</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="app-layout">

<?php require_once __DIR__ . '/../includes/sidebar_admin.php'; ?>

<div class="main-content" id="mainContent">
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div>
                <h1 class="page-title">All Feedbacks</h1>
                <p class="page-subtitle">Browse and filter employee feedback</p>
            </div>
        </div>
    </header>

    <div class="page-content">
        <!-- Filters -->
        <div class="card">
            <div class="card-body">
                <div class="filter-bar">
                    <div class="form-group mb-0">
                        <label for="filterCategory"><i class="fa-solid fa-tag"></i> Category</label>
                        <select id="filterCategory" class="form-select">
                            <option value="">All Categories</option>
                            <option value="work_environment">Work Environment</option>
                            <option value="management">Management</option>
                            <option value="career_growth">Career Growth</option>
                            <option value="team_collaboration">Team Collaboration</option>
                            <option value="work_life_balance">Work-Life Balance</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group mb-0">
                        <label for="filterRating"><i class="fa-solid fa-star"></i> Rating</label>
                        <select id="filterRating" class="form-select">
                            <option value="">All Ratings</option>
                            <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                            <option value="4">⭐⭐⭐⭐ (4)</option>
                            <option value="3">⭐⭐⭐ (3)</option>
                            <option value="2">⭐⭐ (2)</option>
                            <option value="1">⭐ (1)</option>
                        </select>
                    </div>
                    <button id="applyFilters" class="btn btn-primary">
                        <i class="fa-solid fa-filter"></i> Apply
                    </button>
                    <button id="resetFilters" class="btn btn-outline">
                        <i class="fa-solid fa-rotate"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Feedback Table (loaded via AJAX) -->
        <div class="card">
            <div class="card-body p-0">
                <div id="feedbackTableContainer" class="table-wrap">
                    <p class="table-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading…</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/main.js"></script>
<script>
/* Load and reload feedback table */
function loadFeedbacks() {
    const category = document.getElementById('filterCategory').value;
    const rating   = document.getElementById('filterRating').value;
    const params   = new URLSearchParams({ category, rating });

    document.getElementById('feedbackTableContainer').innerHTML =
        '<p class="table-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading…</p>';

    fetch('/api/get_feedbacks.php?' + params.toString())
        .then(r => r.json())
        .then(data => {
            if (!data.success || !data.feedbacks.length) {
                document.getElementById('feedbackTableContainer').innerHTML =
                    '<p class="table-empty"><i class="fa-solid fa-inbox"></i> No feedbacks found.</p>';
                return;
            }
            let html = `<table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee</th>
                        <th>Category</th>
                        <th>Rating</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>`;
            data.feedbacks.forEach((f, i) => {
                const stars = '⭐'.repeat(f.rating);
                const cat   = f.category.replace(/_/g, ' ')
                               .replace(/\b\w/g, c => c.toUpperCase());
                html += `<tr>
                    <td>${i + 1}</td>
                    <td>${f.anonymous ? '<em class="muted">Anonymous</em>' : escHtml(f.employee_name)}</td>
                    <td><span class="badge badge-category">${cat}</span></td>
                    <td class="rating-cell">${stars}</td>
                    <td class="message-cell">${escHtml(f.message)}</td>
                    <td class="nowrap">${f.created_at}</td>
                </tr>`;
            });
            html += '</tbody></table>';
            document.getElementById('feedbackTableContainer').innerHTML = html;
        })
        .catch(() => {
            document.getElementById('feedbackTableContainer').innerHTML =
                '<p class="table-error"><i class="fa-solid fa-triangle-exclamation"></i> Failed to load feedbacks.</p>';
        });
}

function escHtml(str) {
    const d = document.createElement('div');
    d.textContent = str;
    return d.innerHTML;
}

document.getElementById('applyFilters').addEventListener('click', loadFeedbacks);
document.getElementById('resetFilters').addEventListener('click', () => {
    document.getElementById('filterCategory').value = '';
    document.getElementById('filterRating').value   = '';
    loadFeedbacks();
});

loadFeedbacks();
</script>
</body>
</html>
