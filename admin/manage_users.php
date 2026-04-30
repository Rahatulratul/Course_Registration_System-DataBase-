<?php
require_once '../config.php';
require_role('admin');

// Fetch departments for the add user form
$dept_stmt = $pdo->query("SELECT * FROM departments");
$departments = $dept_stmt->fetchAll();

// Add users
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : null;

    try {
        if ($role === 'admin') {
            $stmt = $pdo->prepare("INSERT INTO admins (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $password]);
        } elseif ($role === 'teacher') {
            $stmt = $pdo->prepare("INSERT INTO teachers (name, email, password, department_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $department_id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO students (name, email, password, department_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $department_id]);
        }
        $_SESSION['flash_success'] = "User added successfully.";
    } catch(PDOException $e) {
        $_SESSION['flash_error'] = "Error adding user (email might exist).";
    }
    header("Location: manage_users.php");
    exit;
}

// Delete user
if (isset($_GET['delete']) && isset($_GET['role'])) {
    $id = $_GET['delete'];
    $role = $_GET['role'];
    
    if ($role === 'admin') {
        $pdo->prepare("DELETE FROM admins WHERE id = ?")->execute([$id]);
    } elseif ($role === 'teacher') {
        $pdo->prepare("DELETE FROM teachers WHERE teacher_id = ?")->execute([$id]);
    } else {
        $pdo->prepare("DELETE FROM students WHERE student_id = ?")->execute([$id]);
    }
    
    $_SESSION['flash_success'] = "User deleted.";
    header("Location: manage_users.php");
    exit;
}

$students = $pdo->query("SELECT student_id as id, name, email, 'student' as role FROM students ORDER BY name ASC")->fetchAll();
$teachers = $pdo->query("SELECT teacher_id as id, name, email, 'teacher' as role FROM teachers ORDER BY name ASC")->fetchAll();
$admins = $pdo->query("SELECT id, name, email, 'admin' as role FROM admins ORDER BY name ASC")->fetchAll();

include '../include/header.php';
?>
<div class="glass-panel mb-4">
    <h2><i class="fa-solid fa-users-gear"></i> Manage Users</h2>
</div>

<div class="glass-panel mb-4">
    <h3>Add New User</h3>
    <form method="POST" class="flex gap-4 align-center" style="flex-wrap: wrap;">
        <input type="hidden" name="action" value="add">
        <div class="form-group" style="flex: 1; margin: 0; min-width: 150px;">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="form-group" style="flex: 1; margin: 0; min-width: 150px;">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="form-group" style="flex: 1; margin: 0; min-width: 150px;">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="form-group" style="flex: 1; margin: 0; min-width: 150px;">
            <select name="role" id="add-role-select" class="form-control" required onchange="toggleDept(this.value)">
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="form-group" id="dept-group" style="flex: 1; margin: 0; min-width: 150px;">
            <select name="department_id" class="form-control" id="dept-select" required>
                <option value="">Select Dept</option>
                <?php foreach($departments as $dept): ?>
                    <option value="<?= $dept->id ?>"><?= htmlspecialchars($dept->name) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="margin: 0;"><i class="fa-solid fa-plus"></i> Add</button>
    </form>
</div>

<script>
function toggleDept(role) {
    const deptGroup = document.getElementById('dept-group');
    const deptSelect = document.getElementById('dept-select');
    if (role === 'admin') {
        deptGroup.style.display = 'none';
        deptSelect.removeAttribute('required');
    } else {
        deptGroup.style.display = 'block';
        deptSelect.setAttribute('required', 'required');
    }
}
</script>

<?php
$role_groups = [
    'Students' => $students,
    'Teachers' => $teachers,
    'Administrators' => $admins
];
?>

<?php foreach ($role_groups as $group_title => $group_users): ?>
<div class="glass-panel mb-4">
    <h3><?= $group_title ?></h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($group_users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u->name) ?></td>
                        <td><?= htmlspecialchars($u->email) ?></td>
                        <td>
                            <?php if(!($u->role == 'admin' && $u->id == $_SESSION['user_id'])): ?>
                                <a href="?delete=<?= $u->id ?>&role=<?= $u->role ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?');"><i class="fa-solid fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (count($group_users) === 0): ?>
                    <tr><td colspan="3" class="text-center text-muted">No <?= strtolower($group_title) ?> found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>
<?php include '../include/footer.php'; ?>

