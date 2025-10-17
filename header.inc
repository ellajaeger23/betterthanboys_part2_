<header>
  <nav>
    <img src="styles/Photo/logo.png" alt="BTB Logo" class="logo">
    <ul class="menu">
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="jobs.php">Jobs</a></li>
        <li><a href="apply.php">Apply</a></li>
    </ul>

    <div class="auth" style="margin-left:auto;">
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['username'])): ?>
            <span style="margin-right:10px;">Welcome, <?= htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" class="btn-logout">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn-login">Login</a>
        <?php endif; ?>
    </div>
  </nav>
</header>

<style>
nav {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    background-color: #d64c6c;
    color: #fff;
}
nav .menu {
    display: flex;
    gap: 20px;
    list-style: none;
    margin: 0;
    padding: 0;
}
nav .menu li a {
    color: #fff;
    text-decoration: none;
    font-weight: bold;
}
nav .auth a {
    color: #d64c6c;
    background-color: #fff;
    padding: 6px 12px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
}
nav .auth a:hover {
    background-color: #f0f0f0;
}
</style>
