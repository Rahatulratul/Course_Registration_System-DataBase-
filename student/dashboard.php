<?php
require_once '../config.php';
require_role('student');

$student_id = $_SESSION['user_id'];

// Get total credits and enrolled courses
$stmt = $pdo->prepare("SELECT COUNT(*) as course_count, COALESCE(SUM(c.credits), 0) as total_credits FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ?");
$stmt->execute([$student_id]);
$stats = $stmt->fetch();

include '../include/header.php';
?>
<div class="glass-panel text-center mb-4">
    <h2>Student Dashboard</h2>
    <p style="color:var(--text-muted)">Welcome to your dashboard, <?= htmlspecialchars($_SESSION['name']) ?>.</p>
</div>

<div class="cards-grid">
    <a href="my_courses.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-panel stat-card">
            <div class="icon" style="background: rgba(16, 185, 129, 0.1); color: var(--success);"><i class="fa-solid fa-graduation-cap"></i></div>
            <div class="info">
                <h3><?= $stats->course_count ?></h3>
                <p>Enrolled Courses</p>
            </div>
        </div>
    </a>
    <a href="my_courses.php" style="text-decoration: none; color: inherit; display: block;">
        <div class="glass-panel stat-card">
            <div class="icon" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);"><i class="fa-solid fa-star"></i></div>
            <div class="info">
                <h3><?= $stats->total_credits ?> / 15</h3>
                <p>Total Credits</p>
            </div>
        </div>
    </a>
</div>

<div class="glass-panel mt-8">
    <h3 class="text-center" style="margin-bottom: 20px;">Quick Actions</h3>
    <div class="flex gap-4" style="justify-content: center;">
        <a href="enroll.php" class="btn btn-primary"><i class="fa-solid fa-plus-circle"></i> Browse & Enroll</a>
        <a href="my_courses.php" class="btn btn-secondary" style="background: rgba(255,255,255,0.1); border:1px solid var(--border-color); color:white; padding: 12px 24px; border-radius: 8px;"><i class="fa-solid fa-book-open"></i> My Courses</a>
    </div>
</div>

<?php include '../include/footer.php'; ?>
