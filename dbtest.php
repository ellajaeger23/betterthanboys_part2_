<?php
require_once("settings.php");

$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if ($conn) {
  echo "<p>✅ Connection successful!</p>";
  mysqli_close($conn);
} else {
  echo "<p>❌ Connection failed: " . mysqli_connect_error() . "</p>";
}
?>
