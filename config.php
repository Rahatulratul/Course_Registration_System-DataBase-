<?php
$host = 'localhost';
$dbname = 'course_registration';
$username = 'root';
$password = ''; // Default XAMPP

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch objects by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Helper function to start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL for the project
define('BASE_URL', '/Course_Registration_System/');

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

function has_role($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function require_role($role) {
    require_login();
    if (!has_role($role)) {
        die("Access Denied. You do not have the required role.");
    }
}
?>
