<?php
require_once '../config.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $code = trim($_POST['course_code']);
    $section = trim($_POST['section']);
    $name = trim($_POST['course_name']);
    $credits = (int)$_POST['credits'];
    $faculty_id = !empty($_POST['faculty_id']) ? (int)$_POST['faculty_id'] : NULL;
    $capacity = !empty($_POST['capacity']) ? (int)$_POST['capacity'] : 30;
    $course_time = trim($_POST['course_time'] ?? '');
    $room_number = trim($_POST['room_number'] ?? '');

    $stmt = $pdo->prepare("INSERT INTO courses (course_code, section, course_name, credits, faculty_id, capacity, course_time, room_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    try {
        $stmt->execute([$code, $section, $name, $credits, $faculty_id, $capacity, $course_time, $room_number]);
        $_SESSION['flash_success'] = "Course section added successfully.";
    } catch(PDOException $e) {
        $_SESSION['flash_error'] = "Error: that course section combination might already exist.";
    }
    header("Location: manage_courses.php");
    exit;
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM courses WHERE id = ?")->execute([$id]);
    $_SESSION['flash_success'] = "Course deleted.";
    header("Location: manage_courses.php");
    exit;
}

$courses = $pdo->query("SELECT c.*, t.name as faculty_name, (SELECT COUNT(*) FROM enrollments e WHERE e.course_id = c.id) as filled_seats FROM courses c LEFT JOIN teachers t ON c.faculty_id = t.teacher_id ORDER BY c.course_code ASC, c.section ASC")->fetchAll();
$teachers = $pdo->query("SELECT teacher_id as id, name FROM teachers ORDER BY name ASC")->fetchAll();

include '../include/header.php';
?>
<div class="glass-panel mb-4">
    <h2><i class="fa-solid fa-book"></i> Manage Courses</h2>
</div>

<div class="glass-panel mb-4">
    <h3>Add New Course Section</h3>
    <form method="POST" class="flex gap-4 align-center" style="flex-wrap: wrap;">
        <input type="hidden" name="action" value="add">
        <div class="form-group" style="flex: 1; margin: 0;">
            <input type="text" name="course_code" class="form-control" placeholder="Code (e.g. CSE101)" required>
        </div>
        <div class="form-group" style="flex: 1; margin: 0;">
            <input type="text" name="section" class="form-control" placeholder="Sec (e.g. 1)" required>
        </div>
        <div class="form-group" style="flex: 2; margin: 0;">
            <input type="text" name="course_name" class="form-control" placeholder="Course Name" required>
        </div>
        <div class="form-group" style="flex: 0.5; margin: 0;">
            <input type="number" name="credits" class="form-control" placeholder="Cr" min="1" max="5" required>
        </div>
        <div class="form-group" style="flex: 1.5; margin: 0;">
            <select name="faculty_id" class="form-control">
                <option value="">Select Faculty</option>
                <?php foreach($teachers as $t): ?>
                    <option value="<?= $t->id ?>"><?= htmlspecialchars($t->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="flex: 1; margin: 0;">
            <input type="number" name="capacity" class="form-control" placeholder="Seats (e.g. 30)">
        </div>
        <div class="form-group" style="flex: 1.5; margin: 0;">
            <input type="text" name="course_time" class="form-control" placeholder="Time (e.g. Sun/Tue 08:00)">
        </div>
        <div class="form-group" style="flex: 1; margin: 0;">
            <input type="text" name="room_number" class="form-control" placeholder="Room (e.g. 101)">
        </div>
        <button type="submit" class="btn btn-primary" style="margin: 0;"><i class="fa-solid fa-plus"></i> Add</button>
    </form>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Sec</th>
                <th>Course Name</th>
                <th>Cr</th>
                <th>Faculty</th>
                <th>Time & Room</th>
                <th>Capacity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($courses as $c): ?>
                <tr>
                    <td><strong style="color: var(--primary);"><?= htmlspecialchars($c->course_code) ?></strong></td>
                    <td><?= htmlspecialchars($c->section) ?></td>
                    <td><?= htmlspecialchars($c->course_name) ?></td>
                    <td><?= $c->credits ?></td>
                    <td><?= $c->faculty_name ? htmlspecialchars($c->faculty_name) : '<span style="color:var(--text-muted);">TBA</span>' ?></td>
                    <td>
                        <div style="font-size: 0.9em; color: var(--text-color);">
                            <i class="fa-regular fa-clock text-muted"></i> <?= htmlspecialchars($c->course_time ?: 'TBA') ?><br>
                            <i class="fa-solid fa-door-open text-muted"></i> <?= htmlspecialchars($c->room_number ?: 'TBA') ?>
                        </div>
                    </td>
                    <td><?= $c->filled_seats ?> / <?= htmlspecialchars($c->capacity ?? 'N/A') ?></td>
                    <td>
                        <a href="?delete=<?= $c->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this course section?');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(count($courses) == 0): ?>
                <tr><td colspan="7" class="text-center text-muted">No courses found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include '../include/footer.php'; ?>

