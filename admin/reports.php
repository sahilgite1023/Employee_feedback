<?php
/**
 * admin/reports.php
 * Dedicated reports page with all three Chart.js charts in full width.
 */

$currentPage = 'reports';
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports – FeedbackPro</title>
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
                <h1 class="page-title">Reports & Analytics</h1>
                <p class="page-subtitle">Visual insights into employee feedback trends</p>
            </div>
        </div>
    </header>

    <div class="page-content">
        <!-- Rating Bar chart -->
        <div class="card chart-card-full">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-bar-chart"></i> Feedback Rating Distribution</h2>
            </div>
            <div class="card-body">
                <canvas id="ratingBarChart" height="120"></canvas>
            </div>
        </div>

        <!-- Pie + Line side by side -->
        <div class="charts-grid">
            <div class="card chart-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fa-solid fa-chart-pie"></i> Category Breakdown</h2>
                </div>
                <div class="card-body">
                    <canvas id="categoryPieChart" height="280"></canvas>
                </div>
            </div>

            <div class="card chart-card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fa-solid fa-chart-line"></i> Monthly Trend (12 months)</h2>
                </div>
                <div class="card-body">
                    <canvas id="monthlyLineChart" height="280"></canvas>
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
