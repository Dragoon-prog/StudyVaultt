<?php
require_once __DIR__ . '/../config/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf();

$user_id = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
if ($user_id < 1 || !in_array($status, ['active', 'inactive'], true)) {
    http_response_code(400);
    exit('Invalid account status.');
}

$stmt = $db->prepare("UPDATE users SET status = ? WHERE user_id = ? AND role = 'student'");
$stmt->execute([$status, $user_id]);

flash('success', $stmt->rowCount() ? 'Student account status updated.' : 'No student account was updated.');
redirect('index.php');
