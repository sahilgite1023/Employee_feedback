<?php
/**
 * auth/register.php
 * Employee self-registration page.
 * Admins are created only via direct DB seed or admin panel (not this form).
 */

require_once __DIR__ . '/../includes/auth_check.php';

// Redirect if already logged in
if (!empty($_SESSION['user_id'])) {
    $dest = ($_SESSION['role'] === 'admin') ? '/admin/dashboard.php' : '/employee/dashboard.php';
    header('Location: ' . $dest);
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../config/db.php';

    $name       = trim($_POST['name']       ?? '');
    $email      = trim($_POST['email']      ?? '');
    $department = trim($_POST['department'] ?? '');
    $password   = $_POST['password']        ?? '';
    $confirm    = $_POST['confirm_password'] ?? '';

    // Validation
    if ($name === '' || $email === '' || $password === '') {
        $error = 'Name, email and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $pdo = getPDO();

        // Check duplicate email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'An account with this email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $ins  = $pdo->prepare(
                'INSERT INTO users (name, email, password, role, department) VALUES (?, ?, ?, ?, ?)'
            );
            $ins->execute([$name, $email, $hash, 'employee', $department ?: null]);

            $success = 'Account created successfully! You can now <a href="/auth/login.php">log in</a>.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – Employee Feedback System</title>
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
            <p>Join thousands of employees sharing honest feedback to build better workplaces.</p>
            <ul class="brand-features">
                <li><i class="fa-solid fa-check-circle"></i> Free to use</li>
                <li><i class="fa-solid fa-check-circle"></i> Completely confidential</li>
                <li><i class="fa-solid fa-check-circle"></i> Instant access</li>
            </ul>
        </div>
    </div>

    <!-- Register form panel -->
    <div class="auth-form-panel">
        <div class="auth-form-inner">
            <div class="auth-logo">
                <i class="fa-solid fa-comments"></i>
                <span>FeedbackPro</span>
            </div>
            <h2>Create an account</h2>
            <p class="auth-sub">Start sharing feedback with your organisation</p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-error">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            <?php if ($success !== ''): ?>
                <div class="alert alert-success">
                    <i class="fa-solid fa-circle-check"></i> <?= $success /* already safe – contains a hard-coded anchor */ ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/auth/register.php" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="name"><i class="fa-solid fa-user"></i> Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Jane Doe"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email"><i class="fa-solid fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@company.com"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="department"><i class="fa-solid fa-building"></i> Department <span class="optional">(optional)</span></label>
                    <input type="text" id="department" name="department" placeholder="e.g. Engineering"
                           value="<?= htmlspecialchars($_POST['department'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock"></i> Password</label>
                    <div class="input-icon-right">
                        <input type="password" id="password" name="password" placeholder="Min. 8 characters" required>
                        <button type="button" class="toggle-password" aria-label="Toggle password visibility">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><i class="fa-solid fa-lock"></i> Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Repeat password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i class="fa-solid fa-user-plus"></i> Create Account
                </button>
            </form>

            <p class="auth-switch">
                Already have an account? <a href="/auth/login.php">Sign in</a>
            </p>
        </div>
    </div>
</div>

<script src="/assets/js/main.js"></script>
</body>
</html>
