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

$username = $_SESSION['username'];
$isAdmin = ($username === 'Admin');
$isManager = ($username === 'Manager');

// --- Handle delete by job reference (Admin only) ---
if ($isAdmin && isset($_POST['delete_ref'])) {
    $delete_ref = trim($_POST['delete_ref']);
    $delete_query = "DELETE FROM eoi WHERE job_ref = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, 's', $delete_ref);
    mysqli_stmt_execute($stmt);
    echo "<p class='notice delete'>Deleted all EOIs for Job Reference: " . htmlspecialchars($delete_ref) . "</p>";
    mysqli_stmt_close($stmt);
} elseif ($isManager && isset($_POST['delete_ref'])) {
    echo "<p class='notice warning'>Managers are not allowed to delete EOIs.</p>";
}

// --- Handle status update ---
if (isset($_POST['update_eoi']) && isset($_POST['new_status'])) {
    $eoi_id = $_POST['update_eoi'];
    $new_status = $_POST['new_status'];
    $update_query = "UPDATE eoi SET status = ? WHERE EOInumber = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, 'si', $new_status, $eoi_id);
    mysqli_stmt_execute($stmt);
    echo "<p class='notice success'>Updated EOI #$eoi_id to status: $new_status</p>";
    mysqli_stmt_close($stmt);
}

// --- Filters & sorting ---
$where_clauses = [];
$params = [];
$types = "";

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
if (!empty($_POST['status'])) {
    $where_clauses[] = "status = ?";
    $params[] = $_POST['status'];
    $types .= "s";
}

$allowed_fields = ['EOInumber', 'firstname', 'lastname', 'job_ref', 'status'];
$sort_field = isset($_POST['sort_field']) && in_array($_POST['sort_field'], $allowed_fields)
    ? $_POST['sort_field']
    : 'EOInumber';

$sql = "SELECT * FROM eoi";
if (count($where_clauses) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where_clauses);
}
if ($sort_field === 'EOInumber') {
    $sql .= " ORDER BY CAST(EOInumber AS UNSIGNED)";
} elseif ($sort_field === 'status') {
    $sql .= " ORDER BY 
        CASE status
            WHEN 'New' THEN 1
            WHEN 'Current' THEN 2
            WHEN 'Final' THEN 3
            ELSE 4
        END";
} else {
    $sql .= " ORDER BY $sort_field";
}


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
    <title>Manage EOIs - BTB's Management System</title>
    <link rel="stylesheet" href="styles/style.css">
    <style>
        body {
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
        p {
            text-align: center;
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
        section {
        overflow-x: auto; /* enable horizontal scroll if table is wider */
        }
        table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff; /* keep table visually white */
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
        .notice.success { border-left-color: green; background: #e9fbe9; }
        .notice.delete { border-left-color: #d64c6c; background: #fde5e8; }
        .notice.warning { border-left-color: #f39c12; background: #fff3cd; }

        .logout-btn {
            display: inline-block;
            background-color: #d64c6c;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.2s ease;
        }
        .logout-btn:hover {
            background-color: #c23c5d;
        }
    </style>
</head>
<body>
<?php include "header.inc"; ?>

<section>
    <h1>Welcome, <?= htmlspecialchars($username); ?> 👋</h1>
    <p>Role: <?= $isAdmin ? 'Administrator' : 'Manager'; ?></p>

    <div style="text-align:center; margin-bottom: 20px;">
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>

    <p>Manage all Expressions of Interest (EOIs) below.</p>

    <!-- Filter and Sort Form -->
    <form method="post" action="manage.php">
        <fieldset>
            <legend>Search / Filter EOIs</legend>

            <label>Job Reference:
                <select name="job_ref">
                    <option value="">All</option>
                    <option value="ED001" <?= (isset($_POST['job_ref']) && $_POST['job_ref'] == 'ED001') ? 'selected' : '' ?>>ED001</option>
                    <option value="DL002" <?= (isset($_POST['job_ref']) && $_POST['job_ref'] == 'DL002') ? 'selected' : '' ?>>DL002</option>
                    <option value="IT003" <?= (isset($_POST['job_ref']) && $_POST['job_ref'] == 'IT003') ? 'selected' : '' ?>>IT003</option>
                    <option value="MD004" <?= (isset($_POST['job_ref']) && $_POST['job_ref'] == 'MD004') ? 'selected' : '' ?>>MD004</option>
                </select>
            </label>

            <label>First Name:
                <input type="text" name="firstname" value="<?= isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : '' ?>">
            </label>

            <label>Last Name:
                <input type="text" name="lastname" value="<?= isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : '' ?>">
            </label>

            <label>Status:
                 <select name="status">
                        <option value="">All</option>
                        <option value="New" <?= (isset($_POST['status']) && $_POST['status'] == 'New') ? 'selected' : '' ?>>New</option>
                        <option value="Curent" <?= (isset($_POST['status']) && $_POST['status'] == 'Current') ? 'selected' : '' ?>>Current</option>
                        <option value="Final" <?= (isset($_POST['status']) && $_POST['status'] == 'Final') ? 'selected' : '' ?>>Final</option>
                 </select>
            </label>

            <label>Sort by:
                <select name="sort_field">
                    <?php foreach ($allowed_fields as $field): ?>
                        <option value="<?= $field ?>" <?= ($sort_field == $field) ? 'selected' : '' ?>><?= ucfirst($field) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            
            <input type="submit" value="Search">
        </fieldset>
    </form>

    <?php if ($isAdmin): ?>
    <!-- Delete EOIs by Job Reference -->
    <form method="post" action="manage.php">
        <fieldset>
            <legend>Delete EOIs by Job Reference (Admin Only)</legend>
            <label>Job Reference:
                <select name="delete_ref" required>
                    <option value="">Select...</option>
                    <option value="ED001">ED001</option>
                    <option value="DL002">DL002</option>
                    <option value="IT003">IT003</option>
                    <option value="MD004">MD004</option>
                </select>
            </label>
            <button type="submit">Delete</button>
        </fieldset>
    </form>
    <?php endif; ?>

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
                            <option value='Current' " . ($row['status'] == 'Current' ? 'selected' : '') . ">Current</option>
                            <option value='Final' " . ($row['status'] == 'Final' ? 'selected' : '') . ">Final</option>
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
