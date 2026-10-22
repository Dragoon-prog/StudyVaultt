<?php
require_once __DIR__.'/config/bootstrap.php';
require_login();
$error='';
$subjects=$db->query("SELECT s.*, sem.name semester FROM subjects s JOIN semesters sem ON sem.semester_id=s.semester_id ORDER BY sem.name,s.course_code")->fetchAll();

if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $title=trim($_POST['title']??'');
    $description=trim($_POST['description']??'');
    $subject_id=(int)($_POST['subject_id']??0);
    $file=$_FILES['note_file']??null;
    $allowed=['application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','image/jpeg','image/png'];
    if($title==='' || !$subject_id || !$file || $file['error']!==UPLOAD_ERR_OK) $error='Complete all required fields.';
    elseif($file['size']>10*1024*1024) $error='Maximum file size is 10 MB.';
    elseif(!in_array((new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']),$allowed,true)) $error='Unsupported file type.';
    else{
        $mime=(new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
        $safe=bin2hex(random_bytes(16)).'.'.$ext;
        $dir=__DIR__.'/uploads';
        if(!is_dir($dir)) mkdir($dir,0755,true);
        move_uploaded_file($file['tmp_name'],$dir.'/'.$safe);
        $stmt=$db->prepare("INSERT INTO notes(user_id,subject_id,title,description,file_name,file_path,file_type,file_size) VALUES(?,?,?,?,?,?,?,?)");
        $stmt->execute([$_SESSION['user_id'],$subject_id,$title,$description,$file['name'],'uploads/'.$safe,$mime,$file['size']]);
        flash('success','Note uploaded and sent for admin approval.');
        redirect('index.php');
    }
}
$page_title='Upload Note'; require __DIR__.'/includes/header.php';
?>
<section class="card upload-card">
<div class="upload-heading"><span class="upload-kicker">CONTRIBUTE TO STUDYVAULT</span><h1>Upload a note</h1><p>Share helpful course materials with your classmates. Your upload will be reviewed before it appears in the library.</p></div>
<?php if($error): ?><div class="alert error"><?=e($error)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data" data-validate>
<input type="hidden" name="csrf" value="<?=e(csrf_token())?>">
<div class="form-group"><label for="note-title">Note title</label><input id="note-title" required name="title" value="<?=old('title')?>" placeholder="Give your note a clear title"></div>
<div class="form-group"><label for="subject-id">Subject</label><select id="subject-id" required name="subject_id"><option value="">Select a subject</option><?php foreach($subjects as $s): ?><option value="<?=$s['subject_id']?>"><?=e($s['course_code'].' — '.$s['title'].' ('.$s['semester'].')')?></option><?php endforeach;?></select></div>
<div class="form-group"><label for="note-description">Description <span class="optional-label">(optional)</span></label><textarea id="note-description" name="description" placeholder="Add a short summary to help other students."><?=old('description')?></textarea></div>
<div class="form-group upload-file-group"><label for="note-file">Choose a file</label><input id="note-file" required type="file" name="note_file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"><small>PDF, Word, JPG, or PNG · Maximum file size: 10 MB</small></div>
<button class="button upload-submit" type="submit">Submit note for review</button>
</form>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>
