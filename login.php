<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login page example">
    <meta name="keywords" content="login form, CSS, responsive design">
    <meta name="author" content="Mino">
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css">
    <style>
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 10px;
            font-weight: bold;
            color: #ffffffff;
        }
        input[type="text"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            font-size: 16px;
        }
        input[type="submit"] {
            padding: 10px;
            border-radius: 5px;
            border: none;
            background-color: #d64c6c;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #c23c5d;
        }
        .error {
            color: #d64c6c;
            background-color: #fde5e8;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        /* New part: link style */
        .register-link {
            text-align: center;
            margin-top: 15px;
        }
        .register-link a {
            color: #d64c6c;
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php 
        include 'header.inc';
    ?>
    <section id="login-main">
        <h1>Login</h1>
        <?php
            if (isset($_SESSION['error'])) {
                echo '<div class="error">' . $_SESSION['error'] . '</div>';
                unset($_SESSION['error']);
            }
        ?>
        <form action="process_login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>

        <!--registration link -->
        <div class="register-link">
            <p>Don't have an account? <a href="registration.php">Create one here</a>.</p>
        </div>
    </section>
    <?php 
        include 'footer.inc';
    ?>
</body>
</html>
