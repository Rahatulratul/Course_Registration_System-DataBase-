<?php
require_once 'config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $_SESSION['flash_error'] = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['flash_error'] = "Email already mapped to an account.";
        } else {
            $role = (isset($_POST['role']) && $_POST['role'] === 'teacher') ? 'teacher' : 'student';
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed, $role])) {
                $_SESSION['flash_success'] = "Registration successful! You can now log in.";
                header("Location: login.php");
                exit;
            } else {
                $_SESSION['flash_error'] = "Database error.";
            }
        }
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
        <div class="text-center" style="font-size: 2.5rem; color: var(--accent); margin-bottom: 10px;">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <h2 class="text-center" style="margin-bottom: 30px;">Create Account</h2>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-user" style="position: absolute; left: 16px; top: 16px; color: var(--text-muted);"></i>
                    <input type="text" name="name" class="form-control" style="padding-left: 45px;" required>
                </div>
            </div>
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
                    <i class="fa-solid fa-lock" style="position: absolute; left: 16px; top: 16px; color: var(--text-muted);"></i>
                    <input type="password" name="password" class="form-control" style="padding-left: 45px;" required>
                </div>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-check-double" style="position: absolute; left: 16px; top: 16px; color: var(--text-muted);"></i>
                    <input type="password" name="confirm_password" class="form-control" style="padding-left: 45px;" required>
                </div>
            </div>
            <div class="form-group">
                <label>Account Type</label>
                <div style="position: relative;">
                    <i class="fa-solid fa-id-badge" style="position: absolute; left: 16px; top: 16px; color: var(--text-muted);"></i>
                    <select name="role" id="role-select" class="form-control" style="padding-left: 45px; cursor: pointer;" required>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-4">Register</button>
        </form>
        <p class="text-center mt-4" style="color: var(--text-muted);">
            Already have an account? <a href="login.php">Log In</a>
        </p>
    </div>
</div>
<?php include 'include/footer.php'; ?>
