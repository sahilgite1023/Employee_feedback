<?php
/**
 * api/get_feedbacks.php
 * AJAX endpoint – returns a paginated/filtered list of feedbacks (admin only).
 * Query params: category, rating
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

$allowedCategories = [
    'work_environment', 'management', 'career_growth',
    'team_collaboration', 'work_life_balance', 'other',
];

$category = $_GET['category'] ?? '';
$rating   = (int) ($_GET['rating'] ?? 0);

$where  = [];
$params = [];

if ($category !== '' && in_array($category, $allowedCategories, true)) {
    $where[]  = 'f.category = ?';
    $params[] = $category;
}
if ($rating >= 1 && $rating <= 5) {
    $where[]  = 'f.rating = ?';
    $params[] = $rating;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT
            f.id,
            CASE WHEN f.is_anonymous = 1 THEN NULL ELSE u.name END AS employee_name,
            f.category,
            f.rating,
            f.message,
            f.is_anonymous AS anonymous,
            DATE_FORMAT(f.created_at, '%d %b %Y') AS created_at
        FROM feedback f
        JOIN users u ON u.id = f.user_id
        $whereClause
        ORDER BY f.created_at DESC
        LIMIT 200";

try {
    $pdo  = getPDO();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    echo json_encode(['success' => true, 'feedbacks' => $rows]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
