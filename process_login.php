<?php 
// This code was assisted by Ati lab 10
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

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $input_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // ✅ Verify entered password against hashed password
        if (password_verify($input_password, $user['password'])) {
            // Login successful
            session_regenerate_id(true);

            $_SESSION['username'] = $user['username'];

            // Assign role automatically
            if ($user['username'] === 'Admin') {
                $_SESSION['role'] = 'Admin';
            } elseif ($user['username'] === 'Manager') {
                $_SESSION['role'] = 'Manager';
            } else {
                $_SESSION['role'] = 'User';
            }

            // Redirect based on role
            if ($_SESSION['role'] === 'Admin' || $_SESSION['role'] === 'Manager') {
                header("Location: manage.php");
            } else {
                header("Location: index.php");
            }
            exit;
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
