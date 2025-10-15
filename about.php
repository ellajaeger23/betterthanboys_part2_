<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About</title>
  <link rel="stylesheet" href="styles/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
</head>

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
      <dl>
        <dt>Mino</dt>
        <dd>Responsible for developing the Index and Apply pages, and designing the project logo.</dd>
        <dt>Ella</dt>
        <dd>Responsible for developing the About and Job pages, and Jira management.</dd>
        <dt>Allie</dt>
        <dd>Responsible for developing the Job and Apply pages, and Github.</dd>
      </dl>
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
