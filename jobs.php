<?php
require_once("settings.php");
$conn = mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

$sql = "SELECT * FROM jobs";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="author" content="Uyen Nguyen, Ella Jaeger" />
  <meta name="keywords" content="work, jobs, team, careers" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="styles/style.css" />
  <title>Jobs Available</title>
</head>

<body id="jobs">

  <!-- Include Header -->
  <?php include("header.inc"); ?>

  <main id="jobs-content">
    <!-- Sidebar / Intro -->
    <aside id="sidebarnav" aria-labelledby="apply-intro-title">
      <h1 id="apply-intro-title">Looking for work?</h1>
      <p>
        Join our university’s Digital Learning and Research team.  
        We’re seeking motivated individuals who want to support teaching, learning, and innovation in higher education.
      </p>
      <p>
        To apply, open the role below and hit <strong>Click to apply</strong> — you’ll be taken to our
        application page.
      </p>
    </aside>

    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<section>";
            echo "<h2>" . htmlspecialchars($row['job_title']) . "</h2>";
            echo "<article>";
            echo "<h3>Reference: <code>" . htmlspecialchars($row['job_ref']) . "</code></h3>";
            echo "<p>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
            echo "<p><strong>Salary:</strong> " . htmlspecialchars($row['salary']) . "</p>";
            echo "<p><strong>Reporting line:</strong> " . htmlspecialchars($row['reporting_line']) . "</p>";

            // Responsibilities
            echo "<h3>Key responsibilities</h3><ol>";
            $tasks = explode(';', $row['responsibilities']);
            foreach ($tasks as $task) {
                echo "<li>" . htmlspecialchars(trim($task)) . "</li>";
            }
            echo "</ol>";

            // Essential requirements
            echo "<h3>Essential requirements</h3><ul>";
            $essentials = explode(';', $row['essential_requirements']);
            foreach ($essentials as $req) {
                echo "<li>" . htmlspecialchars(trim($req)) . "</li>";
            }
            echo "</ul>";

            // Preferable requirements
            echo "<h3>Preferable (nice to have)</h3><ul>";
            $prefs = explode(';', $row['preferable_requirements']);
            foreach ($prefs as $pref) {
                echo "<li>" . htmlspecialchars(trim($pref)) . "</li>";
            }
            echo "</ul>";

            echo "<p><a class='btn' href='apply.php?job_ref=" . urlencode($row['job_ref']) . "'>Click to apply</a></p>";
            echo "</article></section>";
        }
    } else {
        echo "<p>No jobs available right now.</p>";
    }

    mysqli_close($conn);
    ?>
  </main>

  <div class="decorations">
    <img src="styles/Photo/cat.png" alt="Pixel cat mascot" class="cat">
    <img src="styles/Photo/doodle1.png" alt="Doodle decoration" class="doodle-left">
    <img src="styles/Photo/doodle2.png" alt="Doodle decoration 1" class="doodle-right">
  </div>

  <!-- Include Footer -->
  <?php include("footer.inc"); ?>

</body>
</html>
