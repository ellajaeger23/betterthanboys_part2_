<?php
session_start();

// Include database connection settings
require_once("settings.php");

// Create a connection to the database
$conn = mysqli_connect($host, $user, $pwd, $sql_db);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize input
    $input_username = trim($_POST['username']);
    $input_password = trim($_POST['password']);

    // Prepared statement to fetch user by username
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $input_username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // ⚠️ Plain-text password check (no hashing)
        if ($input_password === $user['password']) {

            // Store login info in session
            $_SESSION['username'] = $user['username'];

            // Assign role automatically
            if ($user['username'] === 'Admin') {
                $_SESSION['role'] = 'Admin';
            } elseif ($user['username'] === 'Manager') {
                $_SESSION['role'] = 'Manager';
            } else {
                $_SESSION['role'] = 'User';
            }

            // Security: regenerate session ID
            session_regenerate_id(true);

            // Redirect based on role
            if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Manager') {
                header("Location: manage.php");
                exit;
            } else {
                header("Location: index.php");
                exit;
            }

        } else {
            $_SESSION['error'] = "❌ Invalid username or password.";
            header("Location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "❌ User not found.";
        header("Location: login.php");
        exit;
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
