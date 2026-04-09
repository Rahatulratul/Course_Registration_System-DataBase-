<?php
require_once '../config.php';
require_role('admin');

// Add users
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([$name, $email, $password, $role]);
        $_SESSION['flash_success'] = "User added successfully.";
    } catch(PDOException $e) {
        $_SESSION['flash_error'] = "Error adding user (email might exist).";
    }
    header("Location: manage_users.php");
    exit;
}

// Delete user
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    $_SESSION['flash_success'] = "User deleted.";
    header("Location: manage_users.php");
    exit;
}

$students = $pdo->query("SELECT id, name, email, role FROM users WHERE role = 'student' ORDER BY name ASC")->fetchAll();
$teachers = $pdo->query("SELECT id, name, email, role FROM users WHERE role = 'teacher' ORDER BY name ASC")->fetchAll();
$admins = $pdo->query("SELECT id, name, email, role FROM users WHERE role = 'admin' ORDER BY name ASC")->fetchAll();

include '../include/header.php';
?>
<div class="glass-panel mb-4">
    <h2><i class="fa-solid fa-users-gear"></i> Manage Users</h2>
</div>

<div class="glass-panel mb-4">
    <h3>Add New User</h3>
    <form method="POST" class="flex gap-4 align-center" style="flex-wrap: wrap;">
        <input type="hidden" name="action" value="add">
        <div class="form-group" style="flex: 1; margin: 0;">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="form-group" style="flex: 1; margin: 0;">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="form-group" style="flex: 1; margin: 0;">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <div class="form-group" style="flex: 1; margin: 0;">
            <select name="role" class="form-control" required>
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="margin: 0;"><i class="fa-solid fa-plus"></i> Add</button>
    </form>
</div>

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
                            <?php if($u->id != $_SESSION['user_id']): ?>
                                <a href="?delete=<?= $u->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?');"><i class="fa-solid fa-trash"></i></a>
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
