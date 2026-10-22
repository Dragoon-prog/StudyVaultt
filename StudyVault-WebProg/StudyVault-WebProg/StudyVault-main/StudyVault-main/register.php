<?php
require_once __DIR__.'/config/bootstrap.php';
if(is_logged_in()) redirect('browse.php');
$return_to=safe_return_path($_GET['return']??$_POST['return']??null);
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $name=trim($_POST['full_name']??'');
    $email=strtolower(trim($_POST['email']??''));
    $password=$_POST['password']??'';
    if(strlen($name)<2 || !valid_email($email) || strlen($password)<8){
        $error='Please enter a valid name/email and a password of at least 8 characters.';
    } else {
        $check=$db->prepare("SELECT user_id FROM users WHERE email=?"); $check->execute([$email]);
        if($check->fetch()) $error='Email is already registered.';
        else{
            $stmt=$db->prepare("INSERT INTO users(full_name,email,password_hash,status) VALUES(?,?,?,'inactive')");
            $stmt->execute([$name,$email,password_hash($password,PASSWORD_DEFAULT)]);
            flash('success','Registration submitted. An administrator must activate your account before you can log in.');
            redirect('login.php?'.http_build_query(['return'=>$return_to]));
        }
    }
}
$page_title='Register'; require __DIR__.'/includes/header.php';
?>
<div class="card"><h2>Create account</h2>
<?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" data-validate>
<input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<input type="hidden" name="return" value="<?=e($return_to)?>">
<div class="form-group"><label>Full Name</label><input required name="full_name" value="<?=old('full_name')?>"></div>
<div class="form-group"><label>Email</label><input required type="email" name="email" value="<?=old('email')?>"></div>
<div class="form-group"><label>Password</label><input required type="password" name="password" minlength="8"></div>
<button class="button">Register</button>
</form></div>
<?php require __DIR__.'/includes/footer.php'; ?>
