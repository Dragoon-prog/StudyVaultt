<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../includes/functions.php';

$db = (new DatabaseConnection())->connect();

if (isset($_SESSION['user_id'])) {
    $account_check = $db->prepare('SELECT status FROM users WHERE user_id = ? LIMIT 1');
    $account_check->execute([(int)$_SESSION['user_id']]);
    $account_status = $account_check->fetchColumn();
    if ($account_status !== 'active') {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['flash'] = [
            'type' => 'error',
            'message' => $account_status === 'blocked'
                ? 'This account has been blocked. Please contact an administrator.'
                : 'Your account is inactive. Please contact an administrator to activate it.'
        ];
    }
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
