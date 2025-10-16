<?php
session_start();

// Include database connection settings
require_once("settings.php");

// Create a connection to the database
$conn = mysqli_connect($host, $user, $pwd, $sql_db);

// Check if the connection was successful
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if the form was submitted using POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input values from the login form and remove extra spaces
    $input_username = trim($_POST['username']);
    $input_password = trim($_POST['password']);

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $input_username, $input_password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // If a matching user is found
    if ($user = mysqli_fetch_assoc($result)) {
        // Save username to session
        $_SESSION['username'] = $user['username'];

        // Redirect Admin to manage.php
        if ($user['username'] == 'Admin') {
            header('Location: manage.php');
            exit;
        } else {
            // Other users (if any)
            header('Location: index.php');
            exit;
        }
    } else {
        // Invalid credentials
        $_SESSION['error'] = "❌ Invalid username or password.";
        header('Location: login.php');
        exit;
    }

    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>