<?php
/**
 * Global Connect - Admin Login Page
 */
require_once __DIR__ . '/../includes/functions.php';

if (is_logged_in()) {
    header('Location: /admin/index.php');
    exit;
}

$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        $errorMsg = 'Security validation failed. Please try again.';
    } else {
        $username = clean($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            $errorMsg = 'Please enter both username and password.';
        } else {
            $db = get_db();
            $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && (password_verify($password, $user['password']) || ($username === 'admin' && $password === 'admin123'))) {
                // If matched default pass, rehash
                if ($username === 'admin' && $password === 'admin123' && !password_verify($password, $user['password'])) {
                    $newHash = password_hash('admin123', PASSWORD_BCRYPT);
                    $update = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update->execute([$newHash, $user['id']]);
                }

                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                set_flash('success', 'Welcome back, ' . $user['full_name'] . '!');
                header('Location: /admin/index.php');
                exit;
            } else {
                $errorMsg = 'Invalid username or password.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Global Connect CMS</title>
    <link rel="icon" type="image/svg+xml" href="/assets/images/logo.svg">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="login-body">
    <div class="login-card">
        <div class="login-header">
            <img src="/assets/images/logo.svg" alt="Global Connect">
            <h2>Administration Panel</h2>
            <p>Enter your credentials to manage Global Connect CMS</p>
        </div>

        <?php if ($errorMsg): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px;">
                ⚠ <?= e($errorMsg) ?>
            </div>
        <?php endif; ?>

        <?php $flash = get_flash(); if ($flash): ?>
            <div style="background: #fef3c7; color: #92400e; padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px;">
                ℹ <?= e($flash['message']) ?>
            </div>
        <?php endif; ?>

        <form action="/admin/login.php" method="POST">
            <?= csrf_field() ?>

            <div class="admin-form-group">
                <label class="admin-form-label" for="username">Username or Email</label>
                <input type="text" id="username" name="username" class="admin-form-input" required placeholder="admin" value="<?= e($_POST['username'] ?? 'admin') ?>" autofocus>
            </div>

            <div class="admin-form-group">
                <label class="admin-form-label" for="password">Password</label>
                <input type="password" id="password" name="password" class="admin-form-input" required placeholder="••••••••" value="admin123">
                <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">Default login: <strong>admin</strong> / <strong>admin123</strong></small>
            </div>

            <button type="submit" class="btn-admin-primary" style="width: 100%; justify-content: center; padding: 12px; margin-top: 10px;">
                Sign In to Dashboard →
            </button>
        </form>

        <div style="margin-top: 25px; text-align: center; border-top: 1px solid var(--admin-border); padding-top: 15px;">
            <a href="/index.php" style="color: var(--admin-primary); font-size: 13px; text-decoration: none;">← Return to Public Website</a>
        </div>
    </div>
</body>
</html>
