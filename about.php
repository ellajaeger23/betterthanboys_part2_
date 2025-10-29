<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About</title>
  <link rel="stylesheet" href="styles/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>

 <style>
    /* ===== Contributions Table Styling ===== */
    .contributions-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1.5rem;
      background-color: #111;
      color: #fff;
      font-family: 'Orbitron', sans-serif;
      border: 1px solid #333;
      box-shadow: 0 0 10px rgba(255, 122, 194, 0.3);
      border-radius: 8px;
      overflow: hidden;
    }

    .contributions-table th,
    .contributions-table td {
      border: 1px solid #333;
      padding: 12px 16px;
      text-align: left;
      vertical-align: top;
    }

    .contributions-table th {
      background-color: #1a1a1a;
      color: #ff7bc2;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .contributions-table tr:nth-child(even) {
      background-color: #181818;
    }

    .contributions-table tr:hover {
      background-color: #222;
      transition: background-color 0.2s ease-in-out;
    }

    .contributions-table td {
      font-family: 'Courier New', monospace;
      line-height: 1.4;
    }
  </style>
  
<body id="about-page">

  <!-- Include Header -->
  <?php include("header.inc"); ?>
  

  <main id="about-main">
    <!-- About Section -->
     <h1>Insider View of the Team</h1>
    <section id="about-section">
      <h2>About Us</h2>
      <ul>
        <li>Class Time:
          <ul>
            <li>Wednesday 2:30PM to 4:30PM</li>
          </ul>
        </li>
        <li>Student IDs:
          <ul>
            <li>Mino: 105313059</li>
            <li>Ella Jaeger: 105872929</li>
            <li>Allie: 106084686</li>
          </ul>
        </li>
      </ul>

      <p>For BTB to run, we are managed by tutor <strong>Razeen</strong></p>
    </section>

       <!-- Contributions Section -->
    <section id="contributions-section">
      <h2>Contributions</h2>

      <?php
      // --- Database Connection ---
      $host = "localhost";    // your DB host
      $user = "root";         // your DB username
      $pass = "";             // your DB password
      $dbname = "betterthanboys_db";        // your DB name

      // Create connection
      $conn = new mysqli($host, $user, $pass, $dbname);

      // Check connection
      if ($conn->connect_error) {
          die("<p>Connection failed: " . $conn->connect_error . "</p>");
      }

      // Fetch data
      $sql = "SELECT id, name, role, contribution_project1, contribution_project2 FROM members";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
          echo "<table class='contributions-table'>";
          echo "<tr>
                  <th>Name</th>
                  <th>Role</th>
                  <th>Project 1 Contribution</th>
                  <th>Project 2 Contribution</th>
                </tr>";

          while ($row = $result->fetch_assoc()) {
              echo "<tr>
                      <td>" . htmlspecialchars($row['name']) . "</td>
                      <td>" . htmlspecialchars($row['role']) . "</td>
                      <td>" . htmlspecialchars($row['contribution_project1']) . "</td>
                      <td>" . htmlspecialchars($row['contribution_project2']) . "</td>
                    </tr>";
          }

          echo "</table>";
      } else {
          echo "<p>No contributions found.</p>";
      }

      $conn->close();
      ?>

    </section>



    <!-- Group Photo Section -->
    <section id="group-photo-section">
      <h2>Group Photo</h2>
      <figure>
        <img src="styles/Photo/team.jpg" alt="A photo of all three group members.">
        <figcaption>The group!</figcaption>
      </figure>
    </section>

    <!-- Interests Section -->
    <section id="fun-facts-section">
      <h2>Members Fun Facts</h2>
      <table>
        <tr>
          <th>Name</th>
          <th>Mino</th>
          <th>Ella</th>
          <th>Allie</th>
        </tr>
        <tr>
          <th>Dream Job</th>
          <td>Professional horoscope reader</td>
          <td>Prime Minister</td>
          <td>UFC Champion</td>
        </tr>
        <tr>
          <th>Favourite Movie</th>
          <td>Marry My Dead Body</td>
          <td>10 Things I Hate About You</td>
          <td>Jennifer's Body</td>
        </tr>
        <tr>
          <th>Favourite Food</th> 
          <td>Kimbap and Tteokbokki combo</td>
          <td>Sushi</td>
          <td>Spicy Kebab</td>
        </tr>
      </table>
    </section>
  </main>
    
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
