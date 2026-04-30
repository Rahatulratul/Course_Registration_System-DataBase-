<?php
require_once '../config.php';
require_role('admin');

$adminsCount = $pdo->query("SELECT COUNT(*) FROM admins")->fetchColumn();
$studentsCount = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$teachersCount = $pdo->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
$usersCount = $adminsCount + $studentsCount + $teachersCount;
$coursesCount = $pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();

include '../include/header.php';
?>
<div class="glass-panel text-center mb-4">
    <h2>Admin Dashboard</h2>
    <p style="color:var(--text-muted)">System overview and resource management.</p>
</div>

<div class="cards-grid">
    <a href="manage_users.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-panel stat-card">
            <div class="icon" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);"><i class="fa-solid fa-users"></i></div>
            <div class="info">
                <h3><?= $usersCount ?></h3>
                <p>Total Users</p>
            </div>
        </div>
    </a>
    <a href="manage_users.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-panel stat-card">
            <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="info">
                <h3><?= $studentsCount ?></h3>
                <p>Students</p>
            </div>
        </div>
    </a>
    <a href="manage_users.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-panel stat-card">
            <div class="icon" style="background: rgba(236, 72, 153, 0.1); color: var(--accent);"><i class="fa-solid fa-chalkboard-user"></i></div>
            <div class="info">
                <h3><?= $teachersCount ?></h3>
                <p>Teachers</p>
            </div>
        </div>
    </a>
    <a href="manage_courses.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-panel stat-card">
            <div class="icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><i class="fa-solid fa-book-open"></i></div>
            <div class="info">
                <h3><?= $coursesCount ?></h3>
                <p>Courses</p>
            </div>
        </div>
    </a>
</div>

<div class="flex gap-4 mt-8" style="justify-content: center;">
    <a href="manage_courses.php" class="btn btn-primary"><i class="fa-solid fa-book"></i> Manage Courses</a>
    <a href="manage_users.php" class="btn btn-secondary" style="background: rgba(255,255,255,0.1); border:1px solid var(--border-color); color:white;"><i class="fa-solid fa-users-gear"></i> Manage Users</a>
</div>

<?php include '../include/footer.php'; ?>
