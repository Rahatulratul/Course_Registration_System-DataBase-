<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user->password)) {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['name'] = $user->name;
        $_SESSION['role'] = $user->role;
        $_SESSION['flash_success'] = "Welcome back, " . htmlspecialchars($user->name) . "!";
        header("Location: " . BASE_URL . $user->role . "/dashboard.php");
        exit;
    } else {
        $_SESSION['flash_error'] = "Invalid credentials. Admin password defaults to 'admin'.";
    }
}
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
<div style="max-width: 450px; margin: 60px auto;">
    <div class="glass-panel">
        <div class="text-center" style="font-size: 2.5rem; color: var(--primary); margin-bottom: 10px;">
            <i class="fa-solid fa-user-lock"></i>
        </div>
        <h2 class="text-center" style="margin-bottom: 30px;">Welcome Back</h2>
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-envelope" style="position: absolute; left: 16px; top: 16px; color: var(--text-muted);"></i>
                    <input type="email" name="email" class="form-control" style="padding-left: 45px;" required>
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-key" style="position: absolute; left: 16px; top: 16px; color: var(--text-muted);"></i>
                    <input type="password" name="password" class="form-control" style="padding-left: 45px;" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-4">Log In</button>
        </form>
        <p class="text-center mt-4" style="color: var(--text-muted);">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
    </div>
</div>
<?php include 'include/footer.php'; ?>
