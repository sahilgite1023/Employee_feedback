<?php
/**
 * employee/dashboard.php
 * Employee overview: their own stats + quick feedback form link.
 */

$currentPage = 'dashboard';
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('employee');
require_once __DIR__ . '/../config/db.php';

$pdo    = getPDO();
$userId = currentUserId();

// Stats for this employee
$stmt = $pdo->prepare('SELECT COUNT(*) FROM feedback WHERE user_id = ?');
$stmt->execute([$userId]);
$myTotal = (int) $stmt->fetchColumn();

$stmt2 = $pdo->prepare('SELECT AVG(rating) FROM feedback WHERE user_id = ?');
$stmt2->execute([$userId]);
$myAvg = round((float) $stmt2->fetchColumn(), 1);

$stmt3 = $pdo->prepare(
    "SELECT COUNT(*) FROM feedback WHERE user_id = ?
     AND MONTH(created_at) = MONTH(CURDATE())
     AND YEAR(created_at) = YEAR(CURDATE())"
);
$stmt3->execute([$userId]);
$myThisMonth = (int) $stmt3->fetchColumn();

// Recent 5 feedbacks for this employee
$stmt4 = $pdo->prepare(
    'SELECT category, rating, message, is_anonymous, created_at
     FROM feedback WHERE user_id = ?
     ORDER BY created_at DESC LIMIT 5'
);
$stmt4->execute([$userId]);
$recentFeedbacks = $stmt4->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard – FeedbackPro</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="app-layout">

<?php require_once __DIR__ . '/../includes/sidebar_employee.php'; ?>

<div class="main-content" id="mainContent">
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-menu-btn" id="mobileMenuBtn" aria-label="Open menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div>
                <h1 class="page-title">My Dashboard</h1>
                <p class="page-subtitle">Hello, <?= htmlspecialchars($_SESSION['name']) ?>! Here's your activity.</p>
            </div>
        </div>
        <div class="topbar-right">
            <a href="/employee/submit_feedback.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Submit Feedback
            </a>
        </div>
    </header>

    <div class="page-content">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-card-blue">
                <div class="stat-icon"><i class="fa-solid fa-comment-dots"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $myTotal ?></div>
                    <div class="stat-label">My Feedbacks</div>
                </div>
            </div>
            <div class="stat-card stat-card-green">
                <div class="stat-icon"><i class="fa-solid fa-star"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $myAvg > 0 ? $myAvg : '–' ?></div>
                    <div class="stat-label">Avg Rating</div>
                </div>
            </div>
            <div class="stat-card stat-card-orange">
                <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="stat-info">
                    <div class="stat-value"><?= $myThisMonth ?></div>
                    <div class="stat-label">This Month</div>
                </div>
            </div>
        </div>

        <!-- Quick Submit CTA -->
        <div class="card cta-card">
            <div class="cta-icon"><i class="fa-solid fa-bullhorn"></i></div>
            <div class="cta-text">
                <h3>Share your thoughts</h3>
                <p>Your feedback helps improve the workplace for everyone.</p>
            </div>
            <a href="/employee/submit_feedback.php" class="btn btn-primary">
                <i class="fa-solid fa-paper-plane"></i> Submit Feedback
            </a>
        </div>

        <!-- Recent feedbacks -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> My Recent Feedbacks</h2>
                <a href="/employee/my_feedbacks.php" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentFeedbacks)): ?>
                    <p class="table-empty"><i class="fa-solid fa-inbox"></i> You haven't submitted any feedback yet.</p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Rating</th>
                                <th>Message</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($recentFeedbacks as $fb): ?>
                            <tr>
                                <td>
                                    <span class="badge badge-category">
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $fb['category']))) ?>
                                    </span>
                                </td>
                                <td><?= str_repeat('⭐', $fb['rating']) ?></td>
                                <td class="message-cell"><?= htmlspecialchars($fb['message']) ?></td>
                                <td class="nowrap"><?= htmlspecialchars(date('d M Y', strtotime($fb['created_at']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/main.js"></script>
</body>
</html>
