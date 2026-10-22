<?php

require_once __DIR__ . '/../config/bootstrap.php';

require_admin();

/* =========================
   GET PENDING NOTES
   ========================= */

$pending = $db->query("
    SELECT 
        n.*,
        u.full_name,
        s.course_code,
        s.title AS subject_title
    FROM notes n
    JOIN users u 
        ON u.user_id = n.user_id
    JOIN subjects s 
        ON s.subject_id = n.subject_id
    WHERE n.status = 'pending'
    ORDER BY n.created_at
")->fetchAll();


/* =========================
   GET USERS
   ========================= */

$users = $db->query("
    SELECT 
        user_id,
        full_name,
        email,
        role,
        status,
        created_at
    FROM users
    ORDER BY created_at DESC
")->fetchAll();


/* =========================
   PAGE SETUP
   ========================= */

$page_title = 'Admin Dashboard';

require __DIR__ . '/../includes/header.php';
?>

<!-- Admin CSS -->
<link rel="stylesheet" href="../assets/css/admin.css?v=<?= (int) filemtime(__DIR__ . '/../assets/css/admin.css') ?>">


<!-- =========================
     ADMIN DASHBOARD
     ========================= -->

<div class="admin-container">

    <h1>Admin Dashboard</h1>


    <!-- =========================
         PENDING NOTES
         ========================= -->

    <div class="card">

        <div class="card-header">
            <h2>Pending Notes</h2>
            <span class="count">
                <?= count($pending) ?>
            </span>
        </div>

        <div class="table-wrap">

            <table class="table">

                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Uploader</th>
                        <th>Subject</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($pending)): ?>

                        <tr>
                            <td colspan="4" class="empty">
                                No pending notes.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($pending as $n): ?>

                            <tr>

                                <td>
                                    <?= e($n['title']) ?>
                                </td>

                                <td>
                                    <?= e($n['full_name']) ?>
                                </td>

                                <td>
                                    <?= e($n['course_code']) ?>
                                </td>

                                <td class="actions">

                                    <!-- Approve -->
                                    <form
                                        method="post"
                                        action="moderate.php"
                                        class="action-form"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf"
                                            value="<?= e(csrf_token()) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $n['note_id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="approved"
                                        >

                                        <button
                                            type="submit"
                                            class="button"
                                        >
                                            Approve
                                        </button>

                                    </form>


                                    <!-- Reject -->
                                    <form
                                        method="post"
                                        action="moderate.php"
                                        class="action-form"
                                    >

                                        <input
                                            type="hidden"
                                            name="csrf"
                                            value="<?= e(csrf_token()) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= $n['note_id'] ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="status"
                                            value="rejected"
                                        >

                                        <button
                                            type="submit"
                                            class="button danger"
                                        >
                                            Reject
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>


    <!-- =========================
         USERS
         ========================= -->

    <div class="card">

        <div class="card-header">
            <h2>Users</h2>

            <span class="count">
                <?= count($users) ?>
            </span>
        </div>

        <div class="table-wrap">

            <table class="table">

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Account action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($users)): ?>

                        <tr>
                            <td colspan="5" class="empty">
                                No users found.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($users as $u): ?>
                            <?php $status_label = $u['status'] === 'active' ? 'Active' : ($u['status'] === 'blocked' ? 'Blocked' : 'Inactive'); ?>

                            <tr>

                                <td>
                                    <?= e($u['full_name']) ?>
                                </td>

                                <td>
                                    <?= e($u['email']) ?>
                                </td>

                                <td>
                                    <?= e($u['role']) ?>
                                </td>

                                <td>
                                    <span class="status-badge status-<?= e($u['status'] === 'active' ? 'active' : ($u['status'] === 'blocked' ? 'blocked' : 'inactive')) ?>"><?= e($status_label) ?></span>
                                </td>

                                <td class="actions">
                                    <?php if ($u['role'] === 'student' && $u['status'] !== 'blocked'): ?>
                                    <form method="post" action="user_status.php" class="action-form">
                                        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= (int)$u['user_id'] ?>">
                                        <input type="hidden" name="status" value="<?= $u['status'] === 'active' ? 'inactive' : 'active' ?>">
                                        <button type="submit" class="button <?= $u['status'] === 'active' ? 'danger' : '' ?>">
                                            <?= $u['status'] === 'active' ? 'Deactivate' : 'Approve' ?>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="note-meta">—</span>
                                    <?php endif; ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<?php require __DIR__ . '/../includes/footer.php'; ?>
