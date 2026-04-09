<?php
require_once '../config.php';
require_role('teacher');

$teacher_id = $_SESSION['user_id'];
$coursesCount = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE faculty_id = ?");
$coursesCount->execute([$teacher_id]);
$coursesCount = $coursesCount->fetchColumn();

$studentsCount = $pdo->prepare("SELECT COUNT(DISTINCT e.student_id) FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE c.faculty_id = ?");
$studentsCount->execute([$teacher_id]);
$studentsCount = $studentsCount->fetchColumn();

include '../include/header.php';
?>
<div class="glass-panel text-center mb-4">
    <h2>Teacher Dashboard</h2>
    <p style="color:var(--text-muted)">Welcome to your dashboard, <?= htmlspecialchars($_SESSION['name']) ?>.</p>
</div>

<div class="cards-grid">
    <a href="my_courses.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-panel stat-card">
            <div class="icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;"><i class="fa-solid fa-book-open"></i></div>
            <div class="info">
                <h3><?= $coursesCount ?></h3>
                <p>My Courses</p>
            </div>
        </div>
    </a>
    <a href="my_courses.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-panel stat-card">
            <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="info">
                <h3><?= $studentsCount ?></h3>
                <p>Total Students</p>
            </div>
        </div>
    </a>
</div>

<div class="flex gap-4 mt-8" style="justify-content: center;">
    <a href="my_courses.php" class="btn btn-primary"><i class="fa-solid fa-book"></i> View My Courses</a>
</div>

<?php include '../include/footer.php'; ?>
