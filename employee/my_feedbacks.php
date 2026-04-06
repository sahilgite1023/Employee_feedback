<?php
/**
 * employee/my_feedbacks.php
 * Employee view of their own submitted feedback.
 */

$currentPage = 'my_feedbacks';
require_once __DIR__ . '/../includes/auth_check.php';
requireRole('employee');
require_once __DIR__ . '/../config/db.php';

$pdo    = getPDO();
$userId = currentUserId();

$stmt = $pdo->prepare(
    'SELECT id, category, rating, message, is_anonymous, created_at
     FROM feedback WHERE user_id = ?
     ORDER BY created_at DESC'
);
$stmt->execute([$userId]);
$myFeedbacks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Feedbacks – FeedbackPro</title>
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
                <h1 class="page-title">My Feedbacks</h1>
                <p class="page-subtitle">All feedback you have submitted</p>
            </div>
        </div>
        <div class="topbar-right">
            <a href="/employee/submit_feedback.php" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> New Feedback
            </a>
        </div>
    </header>

    <div class="page-content">
        <div class="card">
            <div class="card-body p-0">
                <?php if (empty($myFeedbacks)): ?>
                    <p class="table-empty">
                        <i class="fa-solid fa-inbox"></i>
                        You haven't submitted any feedback yet.
                        <a href="/employee/submit_feedback.php">Submit your first one!</a>
                    </p>
                <?php else: ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Category</th>
                                <th>Rating</th>
                                <th>Message</th>
                                <th>Anonymous</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($myFeedbacks as $i => $fb): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <span class="badge badge-category">
                                        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $fb['category']))) ?>
                                    </span>
                                </td>
                                <td><?= str_repeat('⭐', $fb['rating']) ?></td>
                                <td class="message-cell"><?= htmlspecialchars($fb['message']) ?></td>
                                <td>
                                    <?php if ($fb['is_anonymous']): ?>
                                        <span class="badge badge-anon"><i class="fa-solid fa-user-secret"></i> Yes</span>
                                    <?php else: ?>
                                        <span class="badge badge-visible"><i class="fa-solid fa-user"></i> No</span>
                                    <?php endif; ?>
                                </td>
                                <td class="nowrap">
                                    <?= htmlspecialchars(date('d M Y', strtotime($fb['created_at']))) ?>
                                </td>
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
