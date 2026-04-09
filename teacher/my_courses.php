<?php
require_once '../config.php';
require_role('teacher');

$teacher_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM courses WHERE faculty_id = ? ORDER BY course_code ASC, section ASC");
$stmt->execute([$teacher_id]);
$courses = $stmt->fetchAll();

include '../include/header.php';
?>
<div class="glass-panel mb-4">
    <h2><i class="fa-solid fa-book"></i> My Courses & Students</h2>
</div>

<?php foreach($courses as $c): ?>
    <?php
    $stmt = $pdo->prepare("SELECT u.name, u.email FROM enrollments e JOIN users u ON e.student_id = u.id WHERE e.course_id = ?");
    $stmt->execute([$c->id]);
    $students = $stmt->fetchAll();
    ?>
    <div class="glass-panel mb-4">
        <div class="flex justify-between align-center mb-4">
            <div>
                <h3 style="margin:0;"><span style="color:var(--primary);"><?= htmlspecialchars($c->course_code) ?> [Sec <?= htmlspecialchars($c->section) ?>]</span> - <?= htmlspecialchars($c->course_name) ?></h3>
                <div style="font-size: 0.9em; margin-top: 5px;">
                    <i class="fa-regular fa-clock" style="color:var(--text-muted)"></i> <?= htmlspecialchars($c->course_time ?: 'TBA') ?> &nbsp;|&nbsp; 
                    <i class="fa-solid fa-door-open" style="color:var(--text-muted)"></i> <?= htmlspecialchars($c->room_number ?: 'TBA') ?>
                </div>
            </div>
            <span class="badge badge-teacher"><?= $c->credits ?> Credits</span>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $s): ?>
                        <tr>
                            <td><?= htmlspecialchars($s->name) ?></td>
                            <td><?= htmlspecialchars($s->email) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(count($students) == 0): ?>
                        <tr><td colspan="2" class="text-center" style="color:var(--text-muted)">No students enrolled yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; ?>
<?php if(count($courses) == 0): ?>
    <div class="alert alert-error">You have not been assigned any courses yet.</div>
<?php endif; ?>

<?php include '../include/footer.php'; ?>
