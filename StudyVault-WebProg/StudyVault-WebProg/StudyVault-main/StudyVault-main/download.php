<?php
require_once __DIR__.'/config/bootstrap.php';
if (!is_logged_in()) redirect('login.php?'.http_build_query(['return'=>'browse.php']));
$id=(int)($_GET['id']??0);
$stmt=$db->prepare("SELECT file_path,file_name,file_type FROM notes WHERE note_id=? AND status='approved'");
$stmt->execute([$id]); $note=$stmt->fetch();
if(!$note){http_response_code(404);exit('File not found.');}
$path=__DIR__.'/'.$note['file_path'];
if(!is_file($path)){http_response_code(404);exit('Stored file not found.');}
header('Content-Type: '.$note['file_type']);
header('Content-Disposition: attachment; filename="'.basename($note['file_name']).'"');
header('Content-Length: '.filesize($path));
readfile($path);
exit;
