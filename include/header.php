<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Registration System</title>
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a href="<?= BASE_URL ?>index.php" class="navbar-brand" style="display: flex; align-items: center;">
            <img src="<?= BASE_URL ?>Image/logo.png" alt="Logo" style="height: 48px; margin-right: 12px; background-color: #ffffff; padding: 4px; border-radius: 6px;">
            East West University
        </a>
        <div class="nav-links">
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <a href="<?= BASE_URL ?>admin/dashboard.php">Dashboard</a>
                    <a href="<?= BASE_URL ?>admin/manage_courses.php">Courses</a>
                    <a href="<?= BASE_URL ?>admin/manage_users.php">Users</a>
                <?php elseif($_SESSION['role'] === 'teacher'): ?>
                    <a href="<?= BASE_URL ?>teacher/dashboard.php">Dashboard</a>
                    <a href="<?= BASE_URL ?>teacher/my_courses.php">My Courses</a>
                <?php elseif($_SESSION['role'] === 'student'): ?>
                    <a href="<?= BASE_URL ?>student/dashboard.php">Dashboard</a>
                    <a href="<?= BASE_URL ?>student/enroll.php">Enroll</a>
                    <a href="<?= BASE_URL ?>student/my_courses.php">My Courses</a>
                <?php endif; ?>
                
                <span style="color:var(--text-muted); margin-left: 10px;">| Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
                <a href="<?= BASE_URL ?>logout.php" class="btn btn-danger btn-sm" style="color:#fff; margin-left: 15px;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>login.php" class="btn btn-primary btn-sm" style="color:#fff;">Login</a>
                <a href="<?= BASE_URL ?>register.php" class="btn btn-primary btn-sm" style="color:#fff;">Register</a>
            <?php endif; ?>
        </div>
    </nav>
    <main>
        <?php if(isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $_SESSION['flash_error'] ?>
            </div>
            <?php unset($_SESSION['flash_error']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['flash_success'])): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?= $_SESSION['flash_success'] ?>
            </div>
            <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>
