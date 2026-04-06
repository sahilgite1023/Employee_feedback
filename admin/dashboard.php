<?php
/**
 * admin/dashboard.php
 * Admin overview: summary stats + three Chart.js charts.
 */

$currentPage = 'dashboard';
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('admin');
require_once __DIR__ . '/../config/db.php';

$pdo = getPDO();

// ── Summary stats ──────────────────────────────────────────────────────────
$totalFeedback = (int) $pdo->query('SELECT COUNT(*) FROM feedback')->fetchColumn();
$avgRating     = round((float) $pdo->query('SELECT AVG(rating) FROM feedback')->fetchColumn(), 1);
$totalUsers    = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'employee'")->fetchColumn();
$thisMonth     = (int) $pdo->query(
    "SELECT COUNT(*) FROM feedback WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())"
)->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – FeedbackPro</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="app-layout">

<?php require_once __DIR__ . '/../includes/sidebar_admin.php'; ?>

<div class="main-content" id="mainContent">
    <!-- Top bar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div>
                <h1 class="page-title">Dashboard</h1>
                <p class="page-subtitle">Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>!</p>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-date">
                <i class="fa-solid fa-calendar"></i>
                <?= date('D, d M Y') ?>
            </div>
        </div>
    </header>

    <div class="page-content">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-card-blue">
                <div class="stat-icon"><i class="fa-solid fa-comment-dots"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $totalFeedback ?></div>
                    <div class="stat-label">Total Feedbacks</div>
                </div>
            </div>
            <div class="stat-card stat-card-green">
                <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $avgRating > 0 ? $avgRating : '–' ?></div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
            <div class="stat-card stat-card-purple">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $totalUsers ?></div>
                    <div class="stat-label">Employees</div>
                </div>
            </div>
            <div class="stat-card stat-card-orange">
                <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $thisMonth ?></div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-grid">
            <!-- Bar chart: ratings distribution -->
            <div class="card chart-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fa-solid fa-bar-chart"></i> Rating Distribution</h2>
                </div>
                <div class="card-body">
                    <canvas id="ratingBarChart" height="260"></canvas>
                </div>
            </div>

            <!-- Pie chart: category breakdown -->
            <div class="card chart-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fa-solid fa-chart-pie"></i> Category Breakdown</h2>
                </div>
                <div class="card-body">
                    <canvas id="categoryPieChart" height="260"></canvas>
                </div>
            </div>
        </div>

        <!-- Line chart: monthly trend -->
        <div class="card chart-card-full">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-chart-line"></i> Monthly Feedback Trend</h2>
            </div>
            <div class="card-body">
                <canvas id="monthlyLineChart" height="120"></canvas>
            </div>
        </div>

        <!-- Recent Feedbacks table -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Recent Feedbacks</h2>
                <a href="/admin/feedbacks.php" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div class="card-body p-0">
                <div id="recentFeedbackTable" class="table-wrap">
                    <p class="table-loading"><i class="fa-solid fa-spinner fa-spin"></i> Loading…</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="/assets/js/main.js"></script>
<script src="/assets/js/charts.js"></script>
</body>
</html>
