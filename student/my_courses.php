<?php
require_once '../config.php';
require_role('student');

$student_id = $_SESSION['user_id'];

// Handle dropping a course
if (isset($_GET['drop'])) {
    $course_id = (int)$_GET['drop'];
    $stmt = $pdo->prepare("DELETE FROM enrollments WHERE student_id = ? AND course_id = ?");
    $stmt->execute([$student_id, $course_id]);
    $_SESSION['flash_success'] = "Course dropped successfully.";
    header("Location: my_courses.php");
    exit;
}

$courses = $pdo->query("SELECT c.*, u.name as faculty_name, (SELECT COUNT(*) FROM enrollments e2 WHERE e2.course_id = c.id) as filled_seats FROM enrollments e JOIN courses c ON e.course_id = c.id LEFT JOIN users u ON c.faculty_id = u.id WHERE e.student_id = $student_id ORDER BY c.course_code ASC, c.section ASC")->fetchAll();

include '../include/header.php';
?>
<div class="glass-panel mb-4">
    <h2><i class="fa-solid fa-book-open"></i> My Enrolled Courses</h2>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Sec</th>
                <th>Course Name</th>
                <th>Credits</th>
                <th>Faculty</th>
                <th>Time & Room</th>
                <th>Capacity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($courses as $c): ?>
                <tr>
                    <td><strong style="color: var(--primary);"><?= htmlspecialchars($c->course_code) ?></strong></td>
                    <td><?= htmlspecialchars($c->section) ?></td>
                    <td><?= htmlspecialchars($c->course_name) ?></td>
                    <td><?= $c->credits ?></td>
                    <td><?= $c->faculty_name ? htmlspecialchars($c->faculty_name) : '<span style="color:var(--text-muted)">TBA</span>' ?></td>
                    <td>
                        <div style="font-size: 0.9em; line-height: 1.4;">
                            <i class="fa-regular fa-clock" style="color:var(--text-muted)"></i> <?= htmlspecialchars($c->course_time ?: 'TBA') ?><br>
                            <i class="fa-solid fa-door-open" style="color:var(--text-muted)"></i> <?= htmlspecialchars($c->room_number ?: 'TBA') ?>
                        </div>
                    </td>
                    <td>
                        <?= $c->filled_seats ?> / <?= $c->capacity ?><br>
                        <small style="color: <?= ($c->capacity - $c->filled_seats > 0) ? 'var(--success)' : 'var(--danger)' ?>;">
                            (<?= max(0, $c->capacity - $c->filled_seats) ?> empty)
                        </small>
                    </td>
                    <td>
                        <a href="?drop=<?= $c->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to drop this course?');"><i class="fa-solid fa-circle-minus"></i> Drop</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(count($courses) == 0): ?>
                <tr><td colspan="7" class="text-center" style="color:var(--text-muted);">You are not enrolled in any courses.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include '../include/footer.php'; ?>
