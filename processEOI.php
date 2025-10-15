<?php
// Enable error display (useful for development)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("settings.php");

// Connect to the database
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);
if (!$conn) {
    die("<p>❌ Database connection failed: " . mysqli_connect_error() . "</p>");
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

// If multiple checkboxes are selected, join them into one string
if (is_array($skills)) {
    $skills = implode(", ", $skills);
}

// --- Basic validation ---
if (!$jobref || !$firstname || !$lastname || !$dob || !$gender || !$email) {
    die("<p>❌ Missing required fields. Please go back and fill in all details.</p>");
}

// --- Prepare SQL Insert Query ---
$sql = "INSERT INTO eoi 
        (job_reference_number, first_name, last_name, dob, gender, street_address, suburb, state, postcode, email, phone, skills, other_info)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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

    // --- Execute the query ---
    if (mysqli_stmt_execute($stmt)) {
        // ✅ Success — redirect to thankyou.php
        header("Location: thankyou.php");
        exit;
    } else {
        echo "<p>❌ Error inserting data: " . mysqli_error($conn) . "</p>";
    }

    mysqli_stmt_close($stmt);
} else {
    echo "<p>❌ Database statement failed: " . mysqli_error($conn) . "</p>";
}

// Close the connection
mysqli_close($conn);
?>
