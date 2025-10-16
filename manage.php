<?php
// Start a session
session_start();

// If not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Import DB connection
require_once("settings.php");
$conn = mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("❌ Database connection failed: " . mysqli_connect_error());
}

// Handle delete by job reference
if (isset($_POST['delete_ref'])) {
    $delete_ref = trim($_POST['delete_ref']);
    $delete_query = "DELETE FROM eoi WHERE job_reference_number = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, 's', $delete_ref);
    mysqli_stmt_execute($stmt);
    echo "<p style='color:red;'>Deleted all EOIs for Job Reference: $delete_ref</p>";
    mysqli_stmt_close($stmt);
}

// Handle status update
if (isset($_POST['update_eoi']) && isset($_POST['new_status'])) {
    $eoi_id = $_POST['update_eoi'];
    $new_status = $_POST['new_status'];
    $update_query = "UPDATE eoi SET status = ? WHERE EOInumber = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, 'si', $new_status, $eoi_id);
    mysqli_stmt_execute($stmt);
    echo "<p style='color:green;'>Updated EOI #$eoi_id to status: $new_status</p>";
    mysqli_stmt_close($stmt);
}

// --- Build the query for listing EOIs ---
$where_clauses = [];
$params = [];
$types = "";

// Filter by job reference
if (!empty($_POST['job_ref'])) {
    $where_clauses[] = "job_reference_number LIKE ?";
    $params[] = "%" . $_POST['job_ref'] . "%";
    $types .= "s";
}

// Filter by first name or last name
if (!empty($_POST['first_name'])) {
    $where_clauses[] = "first_name LIKE ?";
    $params[] = "%" . $_POST['first_name'] . "%";
    $types .= "s";
}
if (!empty($_POST['last_name'])) {
    $where_clauses[] = "last_name LIKE ?";
    $params[] = "%" . $_POST['last_name'] . "%";
    $types .= "s";
}

// Sorting
$sort_field = isset($_POST['sort_field']) ? $_POST['sort_field'] : 'EOInumber';

// Build the SQL dynamically
$sql = "SELECT * FROM eoi";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY $sort_field";

// Prepare and execute
$stmt = mysqli_prepare($conn, $sql);

if ($stmt && count($params) > 0) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage EOIs</title>
    <link rel="stylesheet" href="styles/style.css">
</head>
<body>
<?php include "header.inc"; ?>

<section>
    <h1>Welcome, Admin</h1>
    <p>Manage all Expressions of Interest (EOIs) below.</p>

    <!-- Filter and Sort Form -->
    <form method="post" action="manage.php">
        <fieldset>
            <legend>Search / Filter EOIs</legend>
            <label>Job Reference: <input type="text" name="job_ref"></label>
            <label>First Name: <input type="text" name="first_name"></label>
            <label>Last Name: <input type="text" name="last_name"></label>
            <label>Sort by:
                <select name="sort_field">
                    <option value="EOInumber">EOI Number</option>
                    <option value="first_name">First Name</option>
                    <option value="last_name">Last Name</option>
                    <option value="job_reference_number">Job Reference</option>
                    <option value="status">Status</option>
                </select>
            </label>
            <button type="submit">Search</button>
        </fieldset>
    </form>

    <!-- Delete EOIs by Job Reference -->
    <form method="post" action="manage.php">
        <fieldset>
            <legend>Delete EOIs by Job Reference</legend>
            <label>Job Reference: <input type="text" name="delete_ref" required></label>
            <button type="submit">Delete</button>
        </fieldset>
    </form>

    <hr>

    <h2>EOI Records</h2>
    <?php
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>";
        echo "<tr>
                <th>EOI #</th>
                <th>Job Ref</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Change Status</th>
              </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>{$row['EOInumber']}</td>";
            echo "<td>{$row['job_reference_number']}</td>";
            echo "<td>{$row['first_name']} {$row['last_name']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['phone']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "<td>
                    <form method='post' action='manage.php' style='display:inline;'>
                        <input type='hidden' name='update_eoi' value='{$row['EOInumber']}'>
                        <select name='new_status'>
                            <option value='New'>New</option>
                            <option value='Current'>Current</option>
                            <option value='Final'>Final</option>
                        </select>
                        <button type='submit'>Update</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No EOIs found.</p>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    ?>
</section>

<?php include "footer.inc"; ?>
</body>
</html>
