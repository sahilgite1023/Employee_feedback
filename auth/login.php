<?php
/**
 * auth/login.php
 * Login page for both Admin and Employee.
 */

require_once __DIR__ . '/../includes/auth_check.php';

// Already logged in → redirect to correct dashboard
if (!empty($_SESSION['user_id'])) {
    $dest = ($_SESSION['role'] === 'admin') ? '/admin/dashboard.php' : '/employee/dashboard.php';
    header('Location: ' . $dest);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/db.php';

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        $pdo  = getPDO();
        $stmt = $pdo->prepare('SELECT id, name, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Regenerate session id to prevent session fixation
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            $dest = ($user['role'] === 'admin') ? '/admin/dashboard.php' : '/employee/dashboard.php';
            header('Location: ' . $dest);
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Employee Feedback System</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <!-- Branding panel -->
    <div class="auth-brand">
        <div class="brand-content">
            <div class="brand-icon"><i class="fa-solid fa-comments"></i></div>
            <h1>FeedbackPro</h1>
            <p>A modern employee feedback platform that helps organisations grow through honest, structured insights.</p>
            <ul class="brand-features">
                <li><i class="fa-solid fa-check-circle"></i> Anonymous feedback support</li>
                <li><i class="fa-solid fa-check-circle"></i> Real-time analytics dashboard</li>
                <li><i class="fa-solid fa-check-circle"></i> Role-based access control</li>
            </ul>
        </div>
    </div>

    <!-- Login form panel -->
    <div class="auth-form-panel">
        <div class="auth-form-inner">
            <div class="auth-logo">
                <i class="fa-solid fa-comments"></i>
                <span>FeedbackPro</span>
            </div>
            <h2>Welcome back</h2>
            <p class="auth-sub">Sign in to your account to continue</p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/auth/login.php" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@company.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                    <div class="input-icon-right">
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In
                </button>
            </form>

            <p class="auth-switch">
                Don't have an account? <a href="/auth/register.php">Create account</a>
            </p>

            <div class="auth-demo-creds">
                <p><strong>Demo credentials:</strong></p>
                <p>Admin: <code>admin@company.com</code> / <code>Admin@123</code></p>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/main.js"></script>
</body>
</html>
