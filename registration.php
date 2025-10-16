<?php
session_start();
require_once("settings.php");

$conn = mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p class='error'>❌ Database connection failed: " . mysqli_connect_error() . "</p>");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Validation
    if (empty($username) || empty($password) || empty($confirm)) {
        $message = "<p class='error'>⚠️ Please fill in all fields.</p>";
    } elseif ($password !== $confirm) {
        $message = "<p class='error'>❌ Passwords do not match.</p>";
    } else {
        // Check if username exists
        $check_sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $message = "<p class='error'>🚫 Username already exists.</p>";
        } else {
            // ⚠️ Store password in plain text (for testing only)
            $insert_sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($stmt, "ss", $username, $password);
            if (mysqli_stmt_execute($stmt)) {
                $message = "<p class='success'>✅ Registration successful! <a href='login.php'>Login here</a>.</p>";
            } else {
                $message = "<p class='error'>❌ Registration failed. Please try again.</p>";
            }
        }
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Doki’s Management System</title>
    <link rel="stylesheet" href="styles/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000;
            margin: 0;
            color: #333;
        }
        section {
            width: 400px;
            margin: 100px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(214, 76, 108, 0.2);
        }
        h1 {
            color: #d64c6c;
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        input, select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            background-color: #d64c6c;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #c23c5d;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .error {
            color: #d64c6c;
        }
        .success {
            color: green;
        }
        a {
            color: #d64c6c;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<?php include "header.inc"; ?>
<section>
    <h1>Register Account</h1>
    <div class="message"><?= $message ?></div>
    <form method="post" action="registration.php">
        <label>Username:</label>
        <input type="text" name="username" required>

        <label>Password:</label>
        <input type="password" name="password" required>

        <label>Confirm Password:</label>
        <input type="password" name="confirm" required>

        <button type="submit">Register</button>
    </form>

    <p style="text-align:center; margin-top:15px;">Already have an account? <a href="login.php">Login here</a>.</p>
</section>
<?php include "footer.inc"; ?>
</body>
</html>
