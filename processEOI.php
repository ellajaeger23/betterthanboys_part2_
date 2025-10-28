<?php
// Enable error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("settings.php");

// Block direct access if not POST
if ($_SERVER["REQUEST_METHOD"] !== "POST" || empty($_POST)) {
    header("Location: apply.php");
    exit();
}

// Connect to the database
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>❌ Database connection failed: " . mysqli_connect_error() . "</p>");
}

// --- Ensure table exists ---
$createTableSQL = "
CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT AUTO_INCREMENT PRIMARY KEY,
    job_ref VARCHAR(10) NOT NULL,
    firstname VARCHAR(50) NOT NULL,
    lastname VARCHAR(50) NOT NULL,
    dob DATE NOT NULL,
    gender VARCHAR(10),
    street VARCHAR(100),
    suburb VARCHAR(50),
    state VARCHAR(5),
    postcode VARCHAR(10),
    email VARCHAR(100),
    phone VARCHAR(20),
    skills TEXT,
    other_info TEXT,
    status ENUM('New','Current','Final') DEFAULT 'New'
) ENGINE=InnoDB;
";
mysqli_query($conn, $createTableSQL);

// --- Insert sample data if table is empty ---
$checkSample = mysqli_query($conn, "SELECT COUNT(*) AS total FROM eoi");
$count = mysqli_fetch_assoc($checkSample)['total'];

if ($count == 0) {
    $sampleSQL = "
    INSERT INTO eoi (job_ref, firstname, lastname, dob, gender, street, suburb, state, postcode, email, phone, skills, other_info, status)
    VALUES
    ('ED001', 'Alice', 'Nguyen', '1998-03-22', 'Female', '12 Elm Street', 'Melbourne', 'VIC', '3000', 'alice@example.com', '0412345678', 'Communication, Teamwork', 'Experienced in education sector', 'New'),
    ('DL002', 'Ben', 'Tran', '1995-07-19', 'Male', '45 Oak Road', 'Sydney', 'NSW', '2000', 'ben.tran@example.com', 'Leadership, Problem Solving', 'Worked in logistics for 3 years', 'Current'),
    ('IT003', 'Chloe', 'Pham', '2000-12-01', 'Female', '78 Maple Ave', 'Brisbane', 'QLD', '4000', 'chloe.pham@example.com', 'Python, AWS, Security', 'Looking for IT opportunities', 'Final');
    ";
    mysqli_query($conn, $sampleSQL);
}

// --- Collect and clean form data ---
$jobref     = trim($_POST['jobref']);
$firstname  = trim($_POST['firstname']);
$lastname   = trim($_POST['lastname']);
$dob        = trim($_POST['dob']);
$gender     = trim($_POST['sex']);
$street     = trim($_POST['streetaddress']);
$town       = trim($_POST['town']);
$state      = strtoupper(trim($_POST['state']));
$postcode   = trim($_POST['postcode']);
$email      = trim($_POST['email']);
$phone      = trim($_POST['phone']);
$skills     = isset($_POST['skills']) ? $_POST['skills'] : '';
$comments   = trim($_POST['comments']);

// Convert checkbox array to string
if (is_array($skills)) {
    $skills = implode(", ", $skills);
}

// --- Basic server-side validation ---
if (!$jobref || !$firstname || !$lastname || !$dob || !$gender || !$email) {
    die("<p>❌ Missing required fields. Please go back and fill all details.</p>");
}

// --- Insert Record ---
$sql = "INSERT INTO eoi 
(job_ref, firstname, lastname, dob, gender, street, suburb, state, postcode, email, phone, skills, other_info, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'New')";

$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param(
        $stmt,
        'sssssssssssss',
        $jobref,
        $firstname,
        $lastname,
        $dob,
        $gender,
        $street,
        $town,
        $state,
        $postcode,
        $email,
        $phone,
        $skills,
        $comments
    );

    if (mysqli_stmt_execute($stmt)) {
        $eoi_number = mysqli_insert_id($conn);
        echo "<h2>✅ Application Submitted Successfully!</h2>";
        echo "<p>Your Expression of Interest Number is: <strong>{$eoi_number}</strong></p>";
        echo "<p>Status: <strong>New</strong></p>";
        echo "<a href='index.php'>Return to Home</a>";
    } else {
        echo "<p>❌ Error inserting data: " . mysqli_error($conn) . "</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>❌ Database statement failed: " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);
?>
