<?php
/**
 * includes/auth_check.php
 * Centralised session-start + role enforcement.
 * Include at the top of every protected page.
 *
 * Usage:
 *   require_once __DIR__ . '/../includes/auth_check.php';
 *   requireRole('admin');   // or requireRole('employee')
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirect to login if the visitor is not authenticated.
 * Optionally enforce a specific role.
 *
 * @param string|null $role  'admin' | 'employee' | null (any authenticated user)
 */
function requireRole(?string $role = null): void
{
    if (empty($_SESSION['user_id'])) {
        header('Location: /auth/login.php');
        exit;
    }
    if ($role !== null && ($_SESSION['role'] ?? '') !== $role) {
        // Wrong role – send to their own dashboard
        $redirect = ($_SESSION['role'] === 'admin')
            ? '/admin/dashboard.php'
            : '/employee/dashboard.php';
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Return currently logged-in user id (or null).
 */
function currentUserId(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

/**
 * Return currently logged-in user role (or null).
 */
function currentRole(): ?string
{
    return $_SESSION['role'] ?? null;
}
