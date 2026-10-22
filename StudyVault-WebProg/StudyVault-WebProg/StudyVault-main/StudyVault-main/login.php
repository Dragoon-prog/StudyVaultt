<?php
require_once __DIR__.'/config/bootstrap.php';
if(is_logged_in()) redirect('browse.php');
$return_to=safe_return_path($_GET['return']??$_POST['return']??null);
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $email=strtolower(trim($_POST['email']??'')); $password=$_POST['password']??'';
    $stmt=$db->prepare("SELECT * FROM users WHERE email=? LIMIT 1"); $stmt->execute([$email]); $user=$stmt->fetch();
    if(!$user || !password_verify($password,$user['password_hash'])) {
        $error='Invalid email or password.';
    } elseif($user['status'] !== 'active') {
        if ($user['status'] === 'blocked') {
            $error='This account has been blocked. Please contact an administrator.';
        } else {
        $error='Your account is inactive. Please contact an administrator to activate it.';
        }
    } else {
        session_regenerate_id(true);
        $_SESSION['user_id']=$user['user_id']; $_SESSION['role']=$user['role']; $_SESSION['full_name']=$user['full_name'];
        redirect($return_to);
    }
}
$page_title='Login'; require __DIR__.'/includes/header.php';
?>
<section class="auth-layout">
<div class="card auth-card">
<div class="auth-heading"><span class="auth-kicker">WELCOME BACK</span><h1>Log in to StudyVault</h1><p>Find course notes, share helpful resources, and study with your community.</p></div>
<?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" data-validate>
<input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<input type="hidden" name="return" value="<?=e($return_to)?>">
<div class="form-group"><label for="email">Email address</label><input id="email" required type="email" name="email" autocomplete="email" placeholder="you@example.com"></div>
<div class="form-group"><label for="password">Password</label><input id="password" required type="password" name="password" autocomplete="current-password" placeholder="Enter your password"></div>
<button class="button auth-submit" type="submit">Log in</button>
</form>
<p class="auth-switch">New to StudyVault? <a href="register.php?<?=e(http_build_query(['return'=>$return_to]))?>">Create an account</a></p>
</div>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>
