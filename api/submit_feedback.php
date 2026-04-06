<?php
/**
 * api/submit_feedback.php
 * AJAX endpoint – accepts POST and inserts a feedback row.
 * Returns JSON: { success: bool, message: string }
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Must be a logged-in employee (or admin submitting on their own behalf)
if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorised. Please log in.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Input sanitisation ────────────────────────────────────────────────────
$allowedCategories = [
    'work_environment', 'management', 'career_growth',
    'team_collaboration', 'work_life_balance', 'other',
];

$category    = $_POST['category']     ?? '';
$rating      = (int) ($_POST['rating'] ?? 0);
$message     = trim($_POST['message'] ?? '');
$isAnonymous = isset($_POST['is_anonymous']) && $_POST['is_anonymous'] === '1' ? 1 : 0;

// Validate
if (!in_array($category, $allowedCategories, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid category.']);
    exit;
}
if ($rating < 1 || $rating > 5) {
    echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5.']);
    exit;
}
if ($message === '') {
    echo json_encode(['success' => false, 'message' => 'Feedback message cannot be empty.']);
    exit;
}
if (mb_strlen($message) > 2000) {
    echo json_encode(['success' => false, 'message' => 'Message too long (max 2000 characters).']);
    exit;
}

// ── Persist ───────────────────────────────────────────────────────────────
try {
    $pdo  = getPDO();
    $stmt = $pdo->prepare(
        'INSERT INTO feedback (user_id, category, rating, message, is_anonymous)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([currentUserId(), $category, $rating, $message, $isAnonymous]);

    echo json_encode(['success' => true, 'message' => 'Feedback submitted successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
}
