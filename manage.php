<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Import DB connection
require_once("settings.php");
$conn = mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    die("<p class='error'>❌ Database connection failed: " . mysqli_connect_error() . "</p>");
}

// --- Handle delete by job reference ---
if (isset($_POST['delete_ref'])) {
    $delete_ref = trim($_POST['delete_ref']);
    $delete_query = "DELETE FROM eoi WHERE job_ref = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, 's', $delete_ref);
    mysqli_stmt_execute($stmt);
    echo "<p class='notice' style='color:#d64c6c;'>Deleted all EOIs for Job Reference: $delete_ref</p>";
    mysqli_stmt_close($stmt);
}

// --- Handle status update ---
if (isset($_POST['update_eoi']) && isset($_POST['new_status'])) {
    $eoi_id = $_POST['update_eoi'];
    $new_status = $_POST['new_status'];
    $update_query = "UPDATE eoi SET status = ? WHERE EOInumber = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, 'si', $new_status, $eoi_id);
    mysqli_stmt_execute($stmt);
    echo "<p class='notice' style='color:green;'>Updated EOI #$eoi_id to status: $new_status</p>";
    mysqli_stmt_close($stmt);
}

// --- Build the query for listing EOIs ---
$where_clauses = [];
$params = [];
$types = "";

// Filters
if (!empty($_POST['job_ref'])) {
    $where_clauses[] = "job_ref = ?";
    $params[] = $_POST['job_ref'];
    $types .= "s";
}
if (!empty($_POST['firstname'])) {
    $where_clauses[] = "firstname LIKE ?";
    $params[] = "%" . $_POST['firstname'] . "%";
    $types .= "s";
}
if (!empty($_POST['lastname'])) {
    $where_clauses[] = "lastname LIKE ?";
    $params[] = "%" . $_POST['lastname'] . "%";
    $types .= "s";
}

// --- Sorting (safe) ---
$allowed_fields = ['EOInumber', 'firstname', 'lastname', 'job_ref', 'status'];
$sort_field = isset($_POST['sort_field']) && in_array($_POST['sort_field'], $allowed_fields)
    ? $_POST['sort_field']
    : 'EOInumber';

// Build SQL
$sql = "SELECT * FROM eoi";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
$sql .= " ORDER BY " . ($sort_field === 'EOInumber' ? 'CAST(EOInumber AS UNSIGNED)' : $sort_field);

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
    <title>Manage EOIs - Doki's Management System</title>
    <link rel="stylesheet" href="styles/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #000000ff;
            margin: 0;
            color: #2c3e50;
        }
        section {
            width: 85%;
            margin: 40px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(214, 76, 108, 0.15);
        }
        h1, h2 {
            text-align: center;
            color: #d64c6c;
        }
        fieldset {
            border: 1px solid #f5c3cb;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            background: #fffafb;
        }
        legend {
            color: #d64c6c;
            font-weight: bold;
        }
        form {
            display: grid;
            gap: 15px;
        }
        label {
            font-weight: bold;
            color: #2c3e50;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        button, input[type="submit"] {
            padding: 12px 20px;
            border-radius: 5px;
            border: none;
            background-color: #d64c6c;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-top: 8px;
        }
        button:hover, input[type="submit"]:hover {
            background-color: #c23c5d;
        }
        hr {
            margin: 40px 0;
            border: none;
            border-top: 1px solid #f1b6c3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #f0cbd3;
        }
        th {
            background-color: #fbe7ec;
            color: #2c3e50;
        }
        tr:nth-child(even) {
            background-color: #fff5f7;
        }
        tr:hover {
            background-color: #ffeef1;
        }
        .notice {
            background: #fde5e8;
            border-left: 4px solid #d64c6c;
            padding: 10px 15px;
            margin: 10px auto;
            border-radius: 5px;
            width: fit-content;
        }
    </style>
</head>
<body>
<?php include "header.inc"; ?>

<section>
    <h1>Welcome, Admin</h1>
<div style="text-align:center; margin-bottom: 20px;">
    <a href="logout.php" 
       style="
           display: inline-block;
           background-color: #d64c6c;
           color: #fff;
           padding: 10px 20px;
           border-radius: 5px;
           text-decoration: none;
           font-weight: bold;
           transition: background-color 0.2s ease;
       "
       onmouseover="this.style.backgroundColor='#c23c5d'"
       onmouseout="this.style.backgroundColor='#d64c6c'">
       🔒 Logout
    </a>
</div>
    <p style="text-align:center;">Manage all Expressions of Interest (EOIs) below.</p>

    <!-- Filter and Sort Form -->
    <form method="post" action="manage.php">
        <fieldset>
            <legend>Search / Filter EOIs</legend>

            <label>Job Reference:
                <select name="job_ref">
                    <option value="">All</option>
                    <option value="ED001" <?= (isset($_POST['job_ref']) && $_POST['job_ref'] == 'ED001') ? 'selected' : '' ?>>ED001</option>
                    <option value="DL002" <?= (isset($_POST['job_ref']) && $_POST['job_ref'] == 'DL002') ? 'selected' : '' ?>>DL002</option>
                </select>
            </label>

            <label>First Name:
                <input type="text" name="firstname" value="<?= isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : '' ?>">
            </label>

            <label>Last Name:
                <input type="text" name="lastname" value="<?= isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : '' ?>">
            </label>

            <label>Sort by:
                <select name="sort_field">
                    <option value="EOInumber" <?= ($sort_field == 'EOInumber') ? 'selected' : '' ?>>EOI Number</option>
                    <option value="firstname" <?= ($sort_field == 'firstname') ? 'selected' : '' ?>>First Name</option>
                    <option value="lastname" <?= ($sort_field == 'lastname') ? 'selected' : '' ?>>Last Name</option>
                    <option value="job_ref" <?= ($sort_field == 'job_ref') ? 'selected' : '' ?>>Job Reference</option>
                    <option value="status" <?= ($sort_field == 'status') ? 'selected' : '' ?>>Status</option>
                </select>
            </label>

            <input type="submit" value="Search">
        </fieldset>
    </form>

    <!-- Delete EOIs by Job Reference -->
    <form method="post" action="manage.php">
        <fieldset>
            <legend>Delete EOIs by Job Reference</legend>
            <label>Job Reference:
                <select name="delete_ref" required>
                    <option value="">Select...</option>
                    <option value="ED001">ED001</option>
                    <option value="DL002">DL002</option>
                </select>
            </label>
            <button type="submit">Delete</button>
        </fieldset>
    </form>

    <hr>

    <h2>EOI Records</h2>
    <?php
    if (mysqli_num_rows($result) > 0) {
        echo "<table>";
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
            echo "<td>{$row['job_ref']}</td>";
            echo "<td>{$row['firstname']} {$row['lastname']}</td>";
            echo "<td>{$row['email']}</td>";
            echo "<td>{$row['phone']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "<td>
                    <form method='post' action='manage.php' style='display:inline;'>
                        <input type='hidden' name='update_eoi' value='{$row['EOInumber']}'>
                        <select name='new_status'>
                            <option value='New' " . ($row['status'] == 'New' ? 'selected' : '') . ">New</option>
                            <option value='Under Consideration' " . ($row['status'] == 'Under Consideration' ? 'selected' : '') . ">Under Consideration</option>
                            <option value='Hired' " . ($row['status'] == 'Hired' ? 'selected' : '') . ">Hired</option>
                            <option value='Rejected' " . ($row['status'] == 'Rejected' ? 'selected' : '') . ">Rejected</option>
                        </select>
                        <button type='submit'>Update</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='notice'>No EOIs found.</p>";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    ?>
</section>

<?php include "footer.inc"; ?>
</body>
</html>
