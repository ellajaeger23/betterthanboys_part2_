<?php
require_once("settings.php");

// Try to connect
$conn = @mysqli_connect($host, $user, $pwd, $sql_db);

if (!$conn) {
    echo "<p>Database connection failure: " . mysqli_connect_error() . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="Author" content="Mino, Ella, Allison">
  <meta name="keywords" content="apply, offer, join">
  <meta name="description" content="Application data home page">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Application Data Home</title>
  <link rel="stylesheet" href="styles/style.css">
</head>

<body class="apply-page">

  <!-- Include Header -->
  <?php include("header.inc"); ?>

  <section class="apply-hero">
    <h1>Apply</h1>
    <p class="tagline">Join the team. Bring the sparkle 💖</p>
  </section>
  
  <!-- Application Form -->
  <section id="applicationform">
    <h2>Application Form</h2>
    
      <form action="processEOI.php" method="post">


      <div class="form-group full-width">
        <label for="jobref">Job reference number</label>
        <select name="jobref" id="jobref" required>
        <option value="">-- Select a Position --</option>
        <?php
          // Fetch job list dynamically from database (help from GenAI)
          $query = "SELECT job_ref, job_title FROM jobs ORDER BY job_ref ASC";
          $result = mysqli_query($conn, $query);

          if ($result && mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value='" . htmlspecialchars($row['job_ref']) . "'>" .
                        htmlspecialchars($row['job_ref']) . " – " . htmlspecialchars($row['job_title']) .
                       "</option>";
              }
          } else {
              echo "<option disabled>No jobs available</option>";
          }
          ?>
        </select>
      </div>
    </select>
    </div>


      <div class="form-group">
        <label for="firstname">First Name</label>
        <input type="text" name="firstname" id="firstname" maxlength="20" required pattern="[A-Za-z]{1,20}">
      </div>

      <div class="form-group">
        <label for="lastname">Last Name</label>
        <input type="text" name="lastname" id="lastname" maxlength="20" required pattern="[A-Za-z]{1,20}">
      </div>

      <div class="form-group full-width">
        <label for="dob">DOB</label>
        <input type="date" name="dob" id="dob" required>
      </div>

      <fieldset class="form-group full-width gender">
        <legend>Gender</legend>
        <label><input type="radio" name="sex" value="male" required> Male</label>
        <label><input type="radio" name="sex" value="female"> Female</label>
        <label><input type="radio" name="sex" value="non-binary"> Non-binary</label>
        <label><input type="radio" name="sex" value="other"> Other</label>
      </fieldset>

      <div class="form-group full-width">
        <label for="streetaddress">Street Address</label>
        <input type="text" name="streetaddress" id="streetaddress" maxlength="40" required>
      </div>

      <div class="form-group">
        <label for="town">Suburb/Town</label>
        <input type="text" name="town" id="town" maxlength="40" required pattern="[A-Za-z\s]{1,40}">
      </div>

      <div class="form-group">
        <label for="state">State</label>
        <select name="state" id="state">
          <option value="">--Select a State--</option>
          <option value="vic">Victoria</option>
          <option value="nsw">New South Wales</option>
          <option value="tas">Tasmania</option>
          <option value="qld">Queensland</option>
          <option value="sa">South Australia</option>
          <option value="wa">Western Australia</option>
          <option value="nt">Northern Territories</option>
        </select>
      </div>

      <div class="form-group">
        <label for="postcode">Postcode</label>
        <input type="text" name="postcode" id="postcode" maxlength="4" required pattern="\d{4}">
      </div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" required>
      </div>

      <div class="form-group full-width">
        <label for="phone">Phone number</label>
        <input type="tel" name="phone" id="phone" required pattern="\d{8,12}">
      </div>

      <fieldset class="form-group full-width skills">
        <legend>Skills</legend>
        <label><input type="checkbox" name="skills" value="html"> HTML</label>
        <label><input type="checkbox" name="skills" value="css"> CSS</label>
        <label><input type="checkbox" name="skills" value="learning-management-systems"> Learning Management Systems (LMS)</label>
        <label><input type="checkbox" name="skills" value="Zotero"> Zotero</label>
        <label><input type="checkbox" name="skills" value="excel"> Excel</label>
      </fieldset>

      <div class="form-group full-width">
        <label for="comments">Other skills</label>
        <textarea id="comments" name="comments" rows="5"></textarea>
      </div>

      <div class="button-container full-width">
        <input type="submit" value="Submit Form">
        <input type="reset" value="Reset Form">
      </div>

    </form>
  </section> 

  <!-- Decorative characters/images -->
  <div class="decorations">
    <img src="styles/Photo/cat.png" alt="Pixel cat mascot" class="cat">
    <img src="styles/Photo/doodle1.png" alt="Doodle decoration" class="doodle-left">
    <img src="styles/Photo/doodle2.png" alt="Doodle decoration 1" class="doodle-right">
  </div>

  <!-- Include Footer -->
  <?php include("footer.inc"); ?>

</body>
</html>
