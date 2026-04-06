<?php
/**
 * index.php – Root entry point.
 * Redirects authenticated users to their dashboard; others to login.
 */

require_once __DIR__ . '/includes/auth_check.php';

if (!empty($_SESSION['user_id'])) {
    $dest = ($_SESSION['role'] === 'admin') ? '/admin/dashboard.php' : '/employee/dashboard.php';
} else {
    $dest = '/auth/login.php';
}

header('Location: ' . $dest);
exit;
