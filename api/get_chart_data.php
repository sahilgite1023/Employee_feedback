<?php
/**
 * api/get_chart_data.php
 * Returns chart data as JSON for three Chart.js charts (admin only).
 *
 * Response shape:
 * {
 *   "ratings":   { "labels": ["1","2","3","4","5"], "data": [n, n, n, n, n] },
 *   "categories":{ "labels": [...],                 "data": [...] },
 *   "monthly":   { "labels": [...],                 "data": [...] }
 * }
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

try {
    $pdo = getPDO();

    // ── 1. Rating distribution (1–5) ─────────────────────────────────────
    $ratingRows = $pdo->query(
        "SELECT rating, COUNT(*) AS cnt
         FROM feedback
         GROUP BY rating
         ORDER BY rating ASC"
    )->fetchAll();

    $ratingData = array_fill(1, 5, 0); // ratings 1-5 initialised to 0
    foreach ($ratingRows as $row) {
        $ratingData[(int) $row['rating']] = (int) $row['cnt'];
    }

    // ── 2. Category breakdown ─────────────────────────────────────────────
    $categoryRows = $pdo->query(
        "SELECT category, COUNT(*) AS cnt
         FROM feedback
         GROUP BY category
         ORDER BY cnt DESC"
    )->fetchAll();

    $catLabels = [];
    $catData   = [];
    foreach ($categoryRows as $row) {
        $catLabels[] = ucwords(str_replace('_', ' ', $row['category']));
        $catData[]   = (int) $row['cnt'];
    }

    // ── 3. Monthly trend – last 12 months ─────────────────────────────────
    $monthRows = $pdo->query(
        "SELECT DATE_FORMAT(created_at, '%b %Y') AS month_label,
                COUNT(*) AS cnt
         FROM feedback
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 11 MONTH)
         GROUP BY YEAR(created_at), MONTH(created_at)
         ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC"
    )->fetchAll();

    $monthLabels = [];
    $monthData   = [];
    foreach ($monthRows as $row) {
        $monthLabels[] = $row['month_label'];
        $monthData[]   = (int) $row['cnt'];
    }

    echo json_encode([
        'success'    => true,
        'ratings'    => [
            'labels' => array_keys($ratingData),
            'data'   => array_values($ratingData),
        ],
        'categories' => [
            'labels' => $catLabels,
            'data'   => $catData,
        ],
        'monthly'    => [
            'labels' => $monthLabels,
            'data'   => $monthData,
        ],
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
