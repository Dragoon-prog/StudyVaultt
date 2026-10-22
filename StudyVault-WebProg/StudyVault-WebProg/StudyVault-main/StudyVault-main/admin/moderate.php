<?php
require_once __DIR__.'/../config/bootstrap.php';
require_admin();
if($_SERVER['REQUEST_METHOD']!=='POST') redirect('index.php');
verify_csrf();
$id=(int)($_POST['id']??0);
$status=$_POST['status']??'';
if(!in_array($status,['approved','rejected','flagged'],true)){http_response_code(400);exit('Invalid status.');}
$stmt=$db->prepare("UPDATE notes SET status=? WHERE note_id=?"); $stmt->execute([$status,$id]);
flash('success','Note moderation updated.');
redirect('index.php');
