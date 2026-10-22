
<?php
if (!isset($page_title)) $page_title = 'StudyVault';

$db = $GLOBALS['db'] ?? null;
$base_path = str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/') ? '../' : '';
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
$current_page = basename($script_name);
$body_class = str_contains($script_name, '/admin/') ? 'admin-page' : (in_array($current_page, ['index.php', 'browse.php'], true) ? 'home-page' : (in_array($current_page, ['login.php', 'register.php'], true) ? 'auth-page' : ($current_page === 'upload.php' ? 'upload-page' : '')));
$style_version = is_file(__DIR__ . '/../assets/css/style.css') ? filemtime(__DIR__ . '/../assets/css/style.css') : '1';
$admin_has_alerts = false;
if (is_logged_in() && ($_SESSION['role'] ?? '') === 'admin' && $db instanceof PDO) {
    try {
        $alert_count = $db->query("SELECT (SELECT COUNT(*) FROM notes WHERE status = 'pending') + (SELECT COUNT(*) FROM users WHERE role = 'student' AND status = 'inactive')")->fetchColumn();
        $admin_has_alerts = (int)$alert_count > 0;
    } catch (PDOException $notification_error) {
        error_log('StudyVault admin notification count failed: ' . $notification_error->getMessage());
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> | StudyVault</title>
    <link rel="stylesheet" href="<?= e($base_path) ?>assets/css/style.css?v=<?= (int)$style_version ?>">
</head>

<body class="<?= e($body_class) ?>">
    <header class="topbar">
        <a class="brand" href="<?= e($base_path) ?>index.php" aria-label="StudyVault home">
            <img src="<?= e($base_path) ?>assets/images/studyvault-logo.png" alt="StudyVault">
        </a>

        <?php if (!in_array($current_page, ['index.php', 'login.php', 'register.php'], true) || is_logged_in()): ?>
        <nav class="main-nav">
                    <a href="<?= e($base_path) ?>browse.php">Notes</a>

            <?php if (is_logged_in()): ?>
                <a href="<?= e($base_path) ?>upload.php">Upload</a>

                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                    <a class="admin-nav-link" href="<?= e($base_path) ?>admin/index.php"<?= $admin_has_alerts ? ' title="Pending note uploads or student approvals"' : '' ?>>
                        Admin
                        <?php if ($admin_has_alerts): ?><span class="admin-notification-dot" aria-hidden="true"></span><span class="sr-only">Pending note uploads or student approvals</span><?php endif; ?>
                    </a>
                <?php endif; ?>

                <details class="profile-menu">
                    <summary aria-label="Open user profile menu">
                        <span class="profile-avatar" aria-hidden="true"><?= e(strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1))) ?></span>
                        <span class="profile-name"><?= e($_SESSION['full_name'] ?? 'Account') ?></span>
                        <span class="profile-chevron" aria-hidden="true">⌄</span>
                    </summary>

                    <div class="profile-dropdown">
                        <span class="profile-dropdown-name"><?= e($_SESSION['full_name'] ?? 'Account') ?></span>
                        <span class="profile-role"><?= e(ucfirst($_SESSION['role'] ?? 'student')) ?> account</span>
                        <a class="profile-logout" href="<?= e($base_path) ?>logout.php">Log out</a>
                    </div>
                </details>

            <?php else: ?>
                <a href="<?= e($base_path) ?>login.php">Log in</a>
                <a class="button small" href="<?= e($base_path) ?>register.php">Create account</a>
            <?php endif; ?>
        </nav>
        <?php endif; ?>
    </header>

    <main class="container">
        <?php show_flash(); ?>
