<?php
require_once '../config.php';
require_role('student');

$student_id = $_SESSION['user_id'];

// 1. Get current credits
$stmt = $pdo->prepare("SELECT COALESCE(SUM(c.credits), 0) as total_credits FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ?");
$stmt->execute([$student_id]);
$current_credits = $stmt->fetchColumn();

// 2. Handle new enrollment requests
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['course_id'])) {
    $course_id = (int)$_POST['course_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$course_id]);
    $course = $stmt->fetch();
    
    if (!$course) {
        $_SESSION['flash_error'] = "Invalid course selected.";
    } else {
        // Validation Checks
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? AND c.course_code = ?");
        $stmt_check->execute([$student_id, $course->course_code]);
        $already_enrolled = ($stmt_check->fetchColumn() > 0);

        $stmt_cap = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
        $stmt_cap->execute([$course_id]);
        $is_full = ($stmt_cap->fetchColumn() >= $course->capacity);

        $exceeds_credits = (($current_credits + $course->credits) > 15);

        // Check for time clash
        $time_clash = false;
        if (!empty($course->course_time)) {
            $stmt_clash = $pdo->prepare("SELECT c.course_code FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.student_id = ? AND c.course_time = ?");
            $stmt_clash->execute([$student_id, $course->course_time]);
            if ($stmt_clash->rowCount() > 0) {
                $time_clash = true;
            }
        }

        // Apply rules
        if ($already_enrolled) {
            $_SESSION['flash_error'] = "You are already enrolled in a section of this course.";
        } elseif ($time_clash) {
            $_SESSION['flash_error'] = "Cannot enroll. Time clash with another enrolled course.";
        } elseif ($is_full) {
            $_SESSION['flash_error'] = "Cannot enroll. This section is fully booked.";
        } elseif ($exceeds_credits) {
            $_SESSION['flash_error'] = "Cannot enroll. Maximum credit limit (15) exceeded.";
        } else {
            // Success insertion
            try {
                $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
                $stmt->execute([$student_id, $course_id]);
                $_SESSION['flash_success'] = "Successfully enrolled in course.";
            } catch(PDOException $e) {
                $_SESSION['flash_error'] = "Database error. You might already be enrolled.";
            }
        }
    }
    
    header("Location: enroll.php");
    exit;
}

// 3. Fetch courses already taken to mark them disabled in the UI
$enrolled_course_codes = $pdo->query("
    SELECT DISTINCT c.course_code 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    WHERE e.student_id = $student_id
")->fetchAll(PDO::FETCH_COLUMN);

// 4. Fetch all available courses with computed capacity
$available_courses = $pdo->query("
    SELECT c.*, u.name as faculty_name, (SELECT COUNT(*) FROM enrollments e2 WHERE e2.course_id = c.id) as filled_seats 
    FROM courses c 
    LEFT JOIN users u ON c.faculty_id = u.id 
    ORDER BY c.course_code ASC, c.section ASC
")->fetchAll();

include '../include/header.php';
?>
<div class="glass-panel mb-4 flex justify-between align-center">
    <h2><i class="fa-solid fa-plus-circle"></i> Course</h2>
    <div class="badge badge-student" style="font-size: 1rem; padding: 8px 12px;">Credits: <?= $current_credits ?> / 15</div>
</div>

<div class="glass-panel mb-4 text-center" style="padding: 15px;">
    <input type="text" id="courseSearch" class="form-control" placeholder="Search by Course Code or Name..." style="max-width: 500px; display: inline-block; text-align: center; font-size: 1.1rem;">
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
            <?php foreach($available_courses as $c): ?>
                <?php 
                // Compute UI logic variables cleanly
                $is_enrolled = in_array($c->course_code, $enrolled_course_codes);
                $is_full = ($c->filled_seats >= $c->capacity);
                $limit_exceeded = ($current_credits + $c->credits > 15);
                $disabled = ($limit_exceeded || $is_full || $is_enrolled);
                
                $available_seats = max(0, $c->capacity - $c->filled_seats);
                $seat_color = ($available_seats > 0) ? 'var(--success)' : 'var(--danger)';
                $faculty_display = $c->faculty_name ? htmlspecialchars($c->faculty_name) : '<span style="color:var(--text-muted)">TBA</span>';
                ?>
                <tr>
                    <td><strong style="color: var(--primary);"><?= htmlspecialchars($c->course_code) ?></strong></td>
                    <td><?= htmlspecialchars($c->section) ?></td>
                    <td><?= htmlspecialchars($c->course_name) ?></td>
                    <td><?= $c->credits ?></td>
                    <td><?= $faculty_display ?></td>
                    <td>
                        <div style="font-size: 0.9em; line-height: 1.4;">
                            <i class="fa-regular fa-clock" style="color:var(--text-muted)"></i> <?= htmlspecialchars($c->course_time ?: 'TBA') ?><br>
                            <i class="fa-solid fa-door-open" style="color:var(--text-muted)"></i> <?= htmlspecialchars($c->room_number ?: 'TBA') ?>
                        </div>
                    </td>
                    <td>
                        <?= $c->filled_seats ?> / <?= $c->capacity ?><br>
                        <small style="color: <?= $seat_color ?>;">(<?= $available_seats ?> available)</small>
                    </td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="course_id" value="<?= $c->id ?>">
                            <button type="submit" class="btn btn-primary btn-sm" <?= $disabled ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                                <i class="fa-solid fa-right-to-bracket"></i> 
                                <?= $is_enrolled ? 'Enrolled' : 'Enroll' ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if(count($available_courses) == 0): ?>
                <tr><td colspan="8" class="text-center" style="color:var(--text-muted)">No available courses to enroll in.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('courseSearch').addEventListener('keyup', function() {
    let filter = this.value.toUpperCase();
    let tbody = document.querySelector('.table-container table tbody');
    if(tbody) {
        let rows = tbody.getElementsByTagName('tr');
        for (let i = 0; i < rows.length; i++) {
            let codeCol = rows[i].getElementsByTagName('td')[0];
            let nameCol = rows[i].getElementsByTagName('td')[2];
            
            // Only process standard course rows (length > 1 protects against the "No available courses" row)
            if (codeCol && nameCol && rows[i].getElementsByTagName('td').length > 1) {
                let codeTxt = codeCol.textContent || codeCol.innerText;
                let nameTxt = nameCol.textContent || nameCol.innerText;
                
                if (codeTxt.toUpperCase().indexOf(filter) > -1 || nameTxt.toUpperCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    }
});
</script>

<?php include '../include/footer.php'; ?>
