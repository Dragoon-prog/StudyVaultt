<?php
require_once __DIR__.'/config/bootstrap.php';
$page_title='StudyVault — Student Note Sharing';
require __DIR__.'/includes/header.php';
?>
<section class="landing-hero">
    <div class="landing-copy">
        <span class="landing-eyebrow">YOUR CAMPUS STUDY COMMUNITY</span>
        <h1>Good notes make<br>great study sessions.</h1>
        <p>StudyVault helps students find, share, and organize class notes in one place. Spend less time searching and more time learning.</p>
        <a class="button landing-cta" href="login.php">Try StudyVault</a>
        <span class="landing-signup">New to StudyVault? <a href="register.php">Create an account</a></span>
    </div>
    <div class="landing-visual" aria-hidden="true">
        <div class="landing-orbit orbit-one"></div>
        <div class="landing-orbit orbit-two"></div>
        <div class="study-card">
            <span class="study-card-mark">SV</span>
            <span class="study-card-label">STUDY SMARTER</span>
            <span class="study-card-line"></span>
            <span class="study-card-line short"></span>
            <span class="study-card-tag">NOTES · SHARING · SUCCESS</span>
        </div>
    </div>
</section>

<section class="landing-features" aria-labelledby="features-title">
    <div class="landing-section-heading">
        <span class="landing-eyebrow">BUILT FOR STUDENTS</span>
        <h2 id="features-title">Everything you need to study together</h2>
        <p>A simple place to discover helpful course materials and contribute your own.</p>
    </div>
    <div class="feature-grid">
        <article class="feature-card">
            <span class="feature-icon" aria-hidden="true">⌕</span>
            <h3>Find course notes</h3>
            <p>Search study guides, lecture notes, and review materials by subject, semester, or keyword.</p>
        </article>
        <article class="feature-card">
            <span class="feature-icon" aria-hidden="true">↑</span>
            <h3>Share what you know</h3>
            <p>Upload useful class resources and help your classmates prepare for their next study session.</p>
        </article>
        <article class="feature-card">
            <span class="feature-icon" aria-hidden="true">✓</span>
            <h3>Learn with confidence</h3>
            <p>Browse an organized collection of materials reviewed for the StudyVault community.</p>
        </article>
    </div>
</section>

<section class="landing-bottom-cta">
    <div><h2>Ready to find your next helpful note?</h2><p>Log in to start exploring StudyVault.</p></div>
    <a class="button" href="login.php">Try StudyVault</a>
</section>
<?php require __DIR__.'/includes/footer.php'; ?>
