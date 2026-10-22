<?php
require_once __DIR__.'/config/bootstrap.php';
$page_title='Browse Notes';

$q=trim($_GET['q']??'');
$semester=(int)($_GET['semester_id']??0);
$subject=(int)($_GET['subject_id']??0);
$needs_account=!is_logged_in();
$return_to='browse.php?'.http_build_query(['q'=>$q,'semester_id'=>$semester,'subject_id'=>$subject,'search'=>'1']);

$semesters=$db->query("SELECT * FROM semesters ORDER BY name")->fetchAll();
$subjects=$db->query("SELECT * FROM subjects ORDER BY course_code")->fetchAll();

$notes=[];
if (!$needs_account) {
$sql="SELECT * FROM approved_notes WHERE 1=1";
$params=[];
if($q!==''){
    $sql.=" AND (title LIKE ? OR description LIKE ? OR course_code LIKE ? OR subject_title LIKE ?)";
    $term="%$q%"; $params=[$term,$term,$term,$term];
}
if($subject){
    $sql.=" AND course_code = (SELECT course_code FROM subjects WHERE subject_id=?)";
    $params[]=$subject;
}
if($semester){
    $sql.=" AND semester = (SELECT name FROM semesters WHERE semester_id=?)";
    $params[]=$semester;
}
$sql.=" ORDER BY created_at DESC";
$stmt=$db->prepare($sql); $stmt->execute($params); $notes=$stmt->fetchAll();
}

require __DIR__.'/includes/header.php';
?>
<div class="browse-layout">
    <aside class="browse-sidebar">
        <form class="browse-search-panel" method="get">
            <span class="browse-panel-kicker">STUDYVAULT LIBRARY</span>
            <h1>Find your notes</h1>
            <p class="browse-panel-description">Search shared course materials by keyword, subject, or semester.</p>
            <input type="hidden" name="search" value="1">
            <div class="form-group">
                <label for="search-query">Keyword or course code</label>
                <input id="search-query" name="q" placeholder="e.g. Web Programming" value="<?=e($q)?>">
            </div>
            <div class="form-group">
                <label for="search-semester">Year and semester</label>
                <select id="search-semester" name="semester_id"><option value="0">All semesters</option><?php foreach($semesters as $s): ?><option value="<?=$s['semester_id']?>" <?=$semester==$s['semester_id']?'selected':''?>><?=e($s['name'])?></option><?php endforeach;?></select>
            </div>
            <div class="form-group">
                <label for="search-subject">Subject</label>
                <select id="search-subject" name="subject_id"><option value="0">All subjects</option><?php foreach($subjects as $s): ?><option value="<?=$s['subject_id']?>" <?=$subject==$s['subject_id']?'selected':''?>><?=e($s['course_code'].' — '.$s['title'])?></option><?php endforeach;?></select>
            </div>
            <button class="button" type="submit">Search notes</button>
        </form>
    </aside>

    <section class="browse-results" aria-label="Study note results">
        <div class="browse-results-heading">
            <div><span class="browse-panel-kicker">SHARED RESOURCES</span><h2>Study notes</h2></div>
            <?php if(!$needs_account): ?><span class="browse-result-count"><?=count($notes)?> <?=count($notes)===1?'note':'notes'?></span><?php endif; ?>
        </div>

        <?php if($needs_account): ?>
        <section class="card search-auth-prompt" role="status">
            <h2>Log in or create an account to search</h2>
            <p>Already have a StudyVault account? Log in to view matching notes. New here? Create an account to get started.</p>
            <div class="search-auth-actions">
                <a class="button" href="login.php?<?=e(http_build_query(['return'=>$return_to]))?>">Log in</a>
                <a class="button secondary" href="register.php?<?=e(http_build_query(['return'=>$return_to]))?>">Create an account</a>
            </div>
        </section>
        <?php elseif($notes): ?>
        <div class="grid browse-grid">
            <?php foreach($notes as $note): ?>
            <article class="card browse-note-card">
                <div class="browse-note-copy">
                    <h3><?=e($note['title'])?></h3>
                    <p class="note-meta"><?=e($note['course_code'])?> — <?=e($note['subject_title'])?></p>
                    <?php if($note['description']): ?><p><?=e($note['description'])?></p><?php endif; ?>
                    <p class="note-meta"><?=e($note['semester'])?> · <?=e($note['uploader'])?> · <?=date('M d, Y',strtotime($note['created_at']))?></p>
                </div>
                <a class="button" href="download.php?id=<?=$note['note_id']?>">Download</a>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="card browse-empty"><h3>No approved notes found</h3><p>Try another keyword or change your subject and semester filters.</p></div>
        <?php endif; ?>
    </section>
</div>
<?php require __DIR__.'/includes/footer.php'; ?>
