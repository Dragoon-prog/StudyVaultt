<?php
declare(strict_types=1);

function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never {
    header("Location: " . $path);
    exit;
}

function safe_return_path(?string $value): string {
    if ($value === null || $value === '') return 'browse.php';
    $parts = parse_url($value);
    if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || isset($parts['user']) || !in_array($parts['path'] ?? '', ['index.php', 'browse.php'], true)) {
        return 'browse.php';
    }
    return $parts['path'] . (isset($parts['query']) ? '?' . $parts['query'] : '');
}

function csrf_token(): string {
    return $_SESSION['csrf'] ?? '';
}

function verify_csrf(): void {
    $token = $_POST['csrf'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}

function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) redirect('login.php');
}

function require_admin(): void {
    require_login();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('403 Forbidden');
    }
}

function flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type'=>$type, 'message'=>$message];
}

function show_flash(): void {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        echo '<div class="alert '.e($f['type']).'">'.e($f['message']).'</div>';
        unset($_SESSION['flash']);
    }
}

function old(string $key): string {
    return e($_POST[$key] ?? '');
}

function valid_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function valid_course_code(string $code): bool {
    return preg_match('/^[A-Z]{2,10}[0-9]{2,4}[A-Z]?$/i', $code) === 1;
}

function format_bytes(int $bytes): string {
    if ($bytes < 1024) return $bytes . ' B';
    if ($bytes < 1048576) return round($bytes/1024, 1) . ' KB';
    return round($bytes/1048576, 1) . ' MB';
}
