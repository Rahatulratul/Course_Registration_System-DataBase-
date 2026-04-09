<?php
require_once 'config.php';
include 'include/header.php';
?>
<style>
    body {
        background-image: url('<?= BASE_URL ?>assets/img/ewu photo.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-blend-mode: multiply;
        background-color: rgba(15, 23, 42, 0.75);
    }
</style>

<div class="hero-section text-center" style="padding: 100px 20px;">
    <h1>Welcome to East West University</h1>
    <p style="font-size: 1.2rem; color: var(--text-muted); max-width: 600px; margin: 20px auto;">
    </p>
    <div class="flex justify-center gap-4 mt-8" style="justify-content: center;">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn btn-primary" style="font-size: 1.2rem; padding: 16px 32px;"><i
                    class="fa-solid fa-rocket"></i> Register </a>
            <a href="login.php" class="btn btn-secondary"
                style="font-size: 1.2rem; padding: 16px 32px; background: rgba(255,255,255,0.05); border: 1px solid var(--border-color); border-radius: 8px; color: white;"><i
                    class="fa-solid fa-right-to-bracket"></i> Log In</a>
        <?php else: ?>
            <a href="<?= $_SESSION['role'] ?>/dashboard.php" class="btn btn-primary"
                style="font-size: 1.2rem; padding: 16px 32px;"><i class="fa-solid fa-chalkboard-user"></i> Go to
                Dashboard</a>
        <?php endif; ?>
    </div>
</div>

</div>

<?php include 'include/footer.php'; ?>